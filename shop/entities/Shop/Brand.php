<?php
namespace shop\entities\Shop;

use yii\db\ActiveRecord;
use shop\entities\Meta;
use yii\helpers\Json;

class Brand extends  ActiveRecord
{
    public $meta;

    public static function tableName(): string
    {
        return 'shop_brands';
    }

    /**
     * когда мы достали запись из бд
     */
    public function afterFind(): void
    {
        $meta = Json::decode($this->getAttribute('meta_json'));
        $this->meta = new Meta($meta['title'], $meta['description'], $meta['keywords']);

        parent::afterFind();
    }

    /**
     * before save
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        $this->setAttribute('meta_json', Json::encode([
            'title' => $this->meta->title,
            'description' => $this->meta->description,
            'keywords' => $this->meta->keywords
        ]));

        return parent::beforeSave($insert);
    }

    /**
     * @param $name
     * @param $slug
     * @param Meta $meta
     * @return Brand
     */
    public static function create($name, $slug, Meta $meta): self
    {
        $brand = new static();
        $brand->name = $name;
        $brand->slug = $slug;
        $brand->meta = $meta;
        return $brand;
    }

    /**
     * @param $name
     * @param $slug
     * @param Meta $meta
     * @return void
     */
    public function edit($name, $slug, Meta $meta): void
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->meta = $meta;
    }
}