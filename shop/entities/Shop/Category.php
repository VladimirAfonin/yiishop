<?php
namespace shop\entities\Shop;

use paulzi\nestedsets\NestedSetsBehavior;
use shop\behaviors\MetaBehavior;
use shop\queries\CategoryQuery;
use yii\db\ActiveRecord;
use shop\entities\Meta;


class Category extends ActiveRecord
{
    public $meta;

    public static function tableName(): string
    {
        return 'shop_categories';
    }

    /**
     * @param $name
     * @param $slug
     * @param $title
     * @param $description
     * @param Meta $meta
     * @return Category
     */
    public static function create($name, $slug, $title, $description, Meta $meta): self
    {
        $category = new static();
        $category->name = $name;
        $category->slug = $slug;
        $category->title = $title;
        $category->description = $description;
        $category->meta = $meta;
        return $category;
    }

    /**
     * @param $name
     * @param $slug
     * @param $title
     * @param $description
     * @param Meta $meta
     */
    public function edit($name, $slug, $title, $description, Meta $meta): void
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->title = $title;
        $this->description = $description;
        $this->meta = $meta;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            MetaBehavior::className(),
            NestedSetsBehavior::className(),
        ];
    }

    /**
     * из-за 'NestedSetsBehavior' будем оборачивать в транзакции
     * @return array
     */
    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL
        ];
    }

    /**
     * @return CategoryQuery
     */
    public static function find(): CategoryQuery
    {
        return new CategoryQuery(static::class);
    }
}