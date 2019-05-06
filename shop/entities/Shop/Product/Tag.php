<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;

/**
 * @property string $name
 */
class Tag extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'shop_tags';
    }

    /**
     * @param string
     * @return Tag
     */
    public static function create(string $tagName): self
    {
        $tag = new static();
        $tag->name = $tagName;
        return $tag;
    }

}