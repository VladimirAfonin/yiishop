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
                'relations' => ['categoryAssignments', 'values', 'photos', 'tagAssignments', 'relatedAssignments'],
            ]
        ];
    }

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
     * @param $new
     * @param $old
     */
    public function setPrice($new, $old): void
    {
        $this->price_new = $new;
        $this->price_old = $old;
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
        $this->setPhotos($photos);
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
                $this->setPhotos($photos);
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
        $this->setPhotos([]);
    }

    /**
     * @param array $photos
     */
    private function setPhotos(array $photos): void
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
            if($photo->isIdEqualTo($id) && ($prev = $photos[$i - 1] ?? null)) {
                $photos[$i] = $prev;       // current -> prev
                $photos[$i - 1] = $photo; //  prev -> current
                $this->setPhotos($photos);
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
            if($photo->isIdEqualTo($id) && ($next = $photos[$i + 1] ?? null)) {
                $photos[$i] = $next; // current -> next
                $photos[$i + 1] = $photo; // next -> current
                $this->setPhotos($photos);
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
        return $this->hasOne(CategoryAssignment::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getValues(): ActiveQuery
    {
        return $this->hasOne(Value::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPhotos(): ActiveQuery
    {
        return $this->hasOne(Photo::class, ['product_id' => 'id'])->orderBy('sort');
    }

    /**
     * @return ActiveQuery
     */
    public function getTagAssignments(): ActiveQuery
    {
        return $this->hasOne(Tag::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedAssignments(): ActiveQuery
    {
        return $this->hasOne(RelatedAssignment::class, ['product_id' => 'id']);
    }

}