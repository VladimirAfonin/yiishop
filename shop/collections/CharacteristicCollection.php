<?php
namespace shop\collections;

use shop\entities\Shop\Characteristic;

class CharacteristicCollection
{
    /**
     * @param $id
     * @return Characteristic
     */
    public function get($id): Characteristic
    {
        if(!$charact = Characteristic::findOne($id)) { throw new \RuntimeException(('cant find characteristic')); }
        return $charact;
    }

    /**
     * @param Characteristic $characteristic
     */
    public function save(Characteristic $characteristic)
    {
        if(!$characteristic->save()) { throw new NotFoundException('saving error.'); }
    }

    /**
     * @param Characteristic $characteristic
     */
    public function remove(Characteristic $characteristic)
    {
        if(!$characteristic->delete()) { throw new NotFoundException('delete error.'); }
    }
}