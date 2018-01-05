<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;

/*
 * @property integer $characteristic_id
 * @property string $value
 */
class Value extends ActiveRecord
{
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
}