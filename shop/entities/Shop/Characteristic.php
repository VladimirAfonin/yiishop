<?php
namespace shop\entities\Shop;

use yii\db\ActiveRecord;
use yii\helpers\Json;

class Characteristic extends ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';

    public $variants;

    public static function tableName()
    {
        return 'shop_characteristics';
    }

    /**
     * @param $name
     * @param $type
     * @param $required
     * @param $default
     * @param array $variants
     * @param $sort
     * @return Characteristic
     */
    public static function create($name, $type, $required, $default, array $variants, $sort): self
    {
        $object = new static();
        $object->name = $name;
        $object->type = $type;
        $object->required = $required;
        $object->default = $default;
        $object->variants = $variants;
        $object->sort = $sort;
        return $object;
    }

    /**
     * @param $name
     * @param $type
     * @param $required
     * @param $default
     * @param array $variants
     * @param $sort
     */
    public function edit($name, $type, $required, $default, array $variants, $sort): void
    {
        $this->name = $name;
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->default = $default;
        $this->variants = $variants;
        $this->sort = $sort;
    }

    /**
     * if we need 'select' list
     *
     * @return bool
     */
    public function isSelect(): bool
    {
        return count($this->variants) > 0;
    }

    /**
     * after find
     */
    public function afterFind(): void
    {
        $this->variants = Json::encode($this->getAttribute('variants_json'));
        parent::afterFind();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        $this->setAttribute('variants_json', Json::decode($this->variants));
        return parent::beforeSave($insert);
    }

}