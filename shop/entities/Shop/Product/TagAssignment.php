<?php

namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;

/**
 * Class TagAssignment
 * @package shop\entities\Shop\Product
 * @property integer $product_id
 * @property integer $tag_id
 */
class TagAssignment extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'shop_tag_assignments';
    }

    public static function create($tagId): self
    {
        $tag = new static();
        $tag->tag_id = $tagId;
        return $tag;
    }

    public function isForTag($id): bool
    {
        return $this->tag_id == $id;
    }
}