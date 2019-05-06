<?php
namespace shop\entities\Shop;

use yii\db\ActiveRecord;

class Tag extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_tags';
    }

    /**
     * @param $name
     * @param $slug
     * @return Tag
     */
    public static function create($name, $slug): self
    {
        $tag = new static();
        $tag->name = $name;
        $tag->slug = $slug;
        return $tag;
    }

    /**
     * @param $name
     * @param $slug
     */
    public function edit($name, $slug): void
    {
        $this->name = $name;
        $this->slug = $slug;
    }
}