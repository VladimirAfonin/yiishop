<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;

/**
 * @property integer $product_id
 * @property integer $related_id
 *
 * Class RelatedAssignment
 * @package shop\entities\Shop\Product
 */
class RelatedAssignment extends  ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'shop_related_assignments';
    }

    /**
     * @param $productId
     * @return RelatedAssignment
     */
    public static function create($productId): self
    {
        $assignment = new static();
        $assignment->related_id = $productId;
        return $assignment;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isForProduct($id): bool
    {
        return $this->related_id == $id;
    }

}