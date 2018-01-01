<?php
namespace shop\entities\Shop;

use shop\behaviors\MetaBehavior;
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

    public function behaviors(): array
    {
        return [
            // 1-st
            MetaBehavior::class, // our meta behavior
            // 2-st
            /*
            [
                'class' => MetaBehavior::class,
                'jsonAttribute' => 'meta_serialize'
            ]
            */
        ];
    }

    /**
     * когда мы достали запись из бд
     */
//    public function afterFind(): void
//    {
//        parent::afterFind();
//    }

    /**
     * before save
     *
     * @param bool $insert
     * @return bool
     */
//    public function beforeSave($insert): bool
//    {
//        return parent::beforeSave($insert);
//    }

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