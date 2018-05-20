<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use shop\entities\Shop\Characteristic;

/**
 * @property mixed value
 */
class Value extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'shop_values';
    }

    /**
     * @param $charactId
     * @param $value
     * @return Value
     */
    public static function create($charactId, $value): self
    {
        $object = new static();
        $object->characteristic_id = $charactId;
        $object->value = $value;
        return $object;
    }

    /**
     * @param $charactId
     * @return Value
     */
    public static function blank($charactId): self
    {
        $object = new static();
        $object->characteristic_id = $charactId;
        return $object;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isForCharacteristic($id): bool
    {
        return $this->characteristic_id == $id;
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacteristic(): ActiveQuery
    {
        return $this->hasOne(Characteristic::class, ['id' => 'characteristic_id']);
    }
}