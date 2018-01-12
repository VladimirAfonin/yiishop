<?php
namespace shop\entities\Shop\Product;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use SebastianBergmann\CodeCoverage\RuntimeException;
use shop\behaviors\MetaBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use shop\entities\Meta;
use shop\entities\Shop\Brand;
use shop\entities\Shop\Category;
use yii\web\UploadedFile;

class Product extends  ActiveRecord
{
    public $meta;

    /**
     * @return array
     */
    public static function tableName()
    {
        return ['shop_products'];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            MetaBehavior::className(),
            [
                'class' => SaveRelationsBehavior::className(),
                'relations' => [
                    'categoryAssignments',
                    'values',
                    'photos',
                    'tagAssignments',
                    'relatedAssignments',
                    'modifications',
                    'reviews',
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @param $brandId
     * @param $categoryId
     * @param $code
     * @param $name
     * @param Meta $meta
     * @return Product
     */
    public static function create($brandId, $categoryId, $code, $name, Meta $meta): self
    {
        $product = new static();
        $product->brand_id = $brandId;
        $product->category_id = $categoryId;
        $product->code = $code;
        $product->name = $name;
        $product->meta = $meta;
        $product->created_at = time();
        return $product;
    }

    /**
     * @param $brandId
     * @param $code
     * @param $name
     * @param Meta $meta
     */
    public function edit($brandId, $code, $name, Meta $meta): void
    {
        $this->brand_id = $brandId;
        $this->code = $code;
        $this->name = $name;
        $this->meta = $meta;
    }

    /**
     * @param $new
     * @param $old
     */
    public function setPrice($new, $old): void
    {
        $this->price_new = $new;
        $this->price_old = $old;
    }

    /**
     * отзывы
     *
     * @param $userId
     * @param $vote
     * @param $text
     */
    public function addReview($userId, $vote, $text): void
    {
        $reviews = $this->reviews;
        $reviews[] = Review::create($userId, $vote, $text);
        $this->updateReviews($reviews);
    }

    /**
     * общее действие для 'edit', 'activate',
     * 'draft' с Review
     *
     * @param $id
     * @param callable $callback
     */
    private function doWithReview($id, callable  $callback): void
    {
        $reviews = $this->reviews;
        foreach ($reviews as $review) {
            if ($review->isIdEqualTo($id)) {
                $callback();
                $this->updateReviews($reviews);
                return;
            }
        }
        throw new \RuntimeException('review not found');
    }

    /**
     * отзывы редактирование
     *
     * @param $id
     * @param $vote
     * @param $text
     */
    public function editReview($id, $vote, $text): void
    {
        $callback = function (Review $review) use($vote, $text) {
            $review->edit($vote, $text);
        };
        $this->doWithReview($id, $callback);
    }

    /**
     * активировать отзыв
     *
     * @param $id
     */
    public function activateReview($id): void
    {
        $callback = function (Review $review) {
            $review->active();
        };
        $this->doWithReview($id, $callback);
    }

    /**
     * @param $id
     */
    public function draftReview($id): void
    {
        $callback = function (Review $review) {
            $review->draft();
        };
        $this->doWithReview($id, $callback);
    }

    /**
     * удаляем отзыв
     *
     * @param $id
     */
    public function removeReview($id): void
    {
        $reviews = $this->reviews;
        foreach ($reviews as $k => $review) {
            if ($review->isIdEqualTo($id)) {
                unset($reviews[$k]);
                $this->updateReviews($reviews);
                return;
            }
        }
        throw new \RuntimeException('review not found');
    }

    /**
     * перерасчет отзыва
     *
     * @param array $reviews
     */
    private function updateReviews(array $reviews): void
    {
        $amount = 0;
        $total = 0;

        foreach ($reviews as $review) {
            if ($review->isActive()) {
                $amount++;
                $total += $review->getRating();
            }
        }

        $this->reviews = $reviews;
        $this->rating = $amount ? ($total / $amount) : null;
    }

    /**
     * @param $id
     * @param $value
     */
    public function setValue($id, $value): void
    {
        $values = $this->values;
        foreach($values as $item) {
            if($item->isForCharacteristic($id)) {
                return;
            }
        }
        $values[] = Value::create($id, $value);
        $this->values = $values;
    }

    /**
     * @param $id
     * @return Value
     */
    public function getValue($id): Value
    {
        $values = $this->values;
        foreach($values as $item) {
            if($item->isForCharacteristic($id)) {
                return $item;
            }
        }
        return Value::blank($id);
    }

    /**
     * @param $id
     * @return Modification
     */
    public function getModification($id): Modification
    {
        foreach ($this->modifications as $modification) {
            if($modification->isIdEqualTo($id)) {
                return $modification;
            }
        }
        throw new \RuntimeException('modification is not found');
    }

    /**
     * @param $code
     * @param $name
     * @param $price
     */
    public function addModification($code, $name, $price): void
    {
        $modifications = $this->modifications;
        foreach ($modifications as $i => $modification) {
            if ($modification->isCodeEqualTo($code)) {
                throw new \RuntimeException('modification already exists.');
            }
            $modifications[] = Modification::create($code, $name, $price);
            $this->modifications = $modifications;
        }
    }

    /**
     * @param $id
     * @param $code
     * @param $name
     * @param $price
     */
    public function editModification($id, $code, $name, $price): void
    {
        $modifications = $this->modifications;
        foreach ($modifications as $i => $modification) {
            if ($modification->isIdEqualTo($id)) {
                $modification->edit($code, $name, $price);
                $this->modifications = $modifications;
                return;
            }
        }
        throw new \RuntimeException('modification is not found');
    }

    /**
     * @param $categoryId
     */
    public function changeMainCategory($categoryId): void
    {
        $this->category_id = $categoryId;
    }

    /**
     * назначение категории
     *
     * @param $id
     */
    public function assignCategory($id): void
    {
        $assignments = $this->categoryAssignments;
        foreach($assignments as $assignment) {
            if($assignment->isForCategory($id)) {
                return;
            }
        }
        $assignments[] = CategoryAssignment::create($id);
        $this->categoryAssignments = $assignments;
    }

    /**
     * открепляем категорию от товара
     *
     * @param $id
     */
    public function revokeCategory($id): void
    {
        $assignments = $this->categoryAssignments;
        foreach($assignments as $k => $assignment) {
            if($assignment->isForCategory($id)) {
                unset($assignments[$k]);
                $this->categoryAssignments = $assignments;
                return;
            }
        }
        throw new \RuntimeException('assignment is not found.');
    }

    /**
     * открепляем все категории
     */
    public function revokeCategories(): void
    {
        $this->categoryAssignments = [];
    }

    /**
     * add photo
     *
     * @param UploadedFile $file
     */
    public function addPhoto(UploadedFile $file): void
    {
        $photos = $this->photos;
        $photos[] = Photo::create($file);
        $this->updatePhotos($photos);
    }

    /**
     * @param $id
     */
    public function removePhoto($id): void
    {
        $photos = $this->photos;
        foreach($photos as $key => $photo) {
            if($photo->isIdEqualTo($id)) {
                unset($photos[$key]);
                $this->updatePhotos($photos);
                return;
            }
        }
        throw new \RuntimeException('photo is not found');
    }

    /**
     * remove all photos
     */
    public function removePhotos(): void
    {
        $this->updatePhotos([]);
    }

    /**
     * @param array $photos
     */
    private function updatePhotos(array $photos): void
    {
        foreach($photos as $i => $photo) {
            $photo->setSort($i);
        }
        $this->photos = $photos;
    }

    /**
     * sort photo to up
     *
     * @param $id
     */
    public function movePhotoDown($id): void
    {
        $photos = $this->photos;
        foreach ($photos as $i => $photo) {
            if($photo->isIdEqualTo($id)) {
                if($prev = $photos[$i - 1] ?? null) {
                    $photos[$i] = $prev;       // current -> prev
                    $photos[$i - 1] = $photo; //  prev -> current
                    $this->updatePhotos($photos);
                }
                return;
            }
        }
        throw new RuntimeException('photo is not found');
    }

    /**
     * sort photo to down
     *
     * @param $id
     */
    public function movePhotoUp($id): void
    {
        $photos = $this->photos;
        foreach ($photos as $i => $photo) {
            if($photo->isIdEqualTo($id)) {
                if($next = $photos[$i + 1] ?? null) {
                    $photos[$i] = $next; // current -> next
                    $photos[$i + 1] = $photo; // next -> current
                    $this->updatePhotos($photos);
                }
                return;
            }
        }
        throw new RuntimeException('photo is not found');
    }

    /**
     * @param $id
     */
    public function assignTag($id): void
    {
        $assignments = $this->tagAssignments;
        foreach($assignments as $assignment) {
            if($assignment->isForTag($id)) {
                return;
            }
        }
        $assignments[] = CategoryAssignment::create($id);
        $this->tagAssignments = $assignments;
    }

    /**
     * one tag
     *
     * @param $id
     */
    public function revokeTag($id): void
    {
        $assignments = $this->tagAssignments;
        foreach ($assignments as $k => $assignment) {
            if ($assignment->isForTag($id)) {
                unset($assignments[$k]);
                $this->tagAssignments = $assignments;
                return;
            }
        }
        throw new \RuntimeException('assignment is not found.');
    }

    /**
     * all tags
     */
    public function revokeTags(): void
    {
        $this->tagAssignments = [];
    }

    /**
     * assign related product
     *
     * @param $id
     */
    public function assignRelatedProduct($id): void
    {
        $assignments = $this->relatedAssignments;
        foreach ($assignments as $assignment) {
            if ($assignment->isForProduct($id)) {
                return;
            }
        }
        $assignments[] = CategoryAssignment::create($id);
        $this->relatedAssignments = $assignments;
    }

    /**
     * revoke related product
     *
     * @param $id
     */
    public function revokeRelatedProduct($id): void
    {
        $assignments = $this->relatedAssignments;
        foreach ($assignments as $k => $assignment) {
            if ($assignment->isForProduct($id)) {
                unset($assignments[$k]);
                $this->relatedAssignments = $assignments;
                return;
            }
        }
        throw new \RuntimeException('assignment is not found.');
    }


    /**
     * @return ActiveQuery
     */
    public function getBrand(): ActiveQuery
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryAssignments(): ActiveQuery
    {
        return $this->hasMany(CategoryAssignment::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getValues(): ActiveQuery
    {
        return $this->hasMany(Value::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPhotos(): ActiveQuery
    {
        return $this->hasMany(Photo::class, ['product_id' => 'id'])->orderBy('sort');
    }

    /**
     * @return ActiveQuery
     */
    public function getTagAssignments(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedAssignments(): ActiveQuery
    {
        return $this->hasMany(RelatedAssignment::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getModifications(): ActiveQuery
    {
        return $this->hasMany(Modification::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMainPhoto(): ActiveQuery
    {
        return $this->hasOne(Photo::class, ['id' => 'main_photo_id']);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes): void
    {
        $related = $this->getRelatedRecords();
        if(array_key_exists('mainPhoto', $related)) {
            $this->updateAttributes(['main_photo_id' => ($related['mainPhoto'] ? $related['mainPhoto']->id : null)]);
        }
        parent::afterSave($insert, $changedAttributes);
    }

}