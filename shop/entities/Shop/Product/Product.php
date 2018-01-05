<?php
namespace shop\entities\Shop\Product;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use shop\behaviors\MetaBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use shop\entities\Meta;
use shop\entities\Shop\Brand;
use shop\entities\Shop\Category;

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
                'relations' => ['categoryAssignments'],
            ]
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
}