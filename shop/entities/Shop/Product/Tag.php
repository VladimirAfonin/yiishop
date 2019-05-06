<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;

/*
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
        $photo = new static();
        $photo->tagName = $tagName;
        return $photo;
    }

}