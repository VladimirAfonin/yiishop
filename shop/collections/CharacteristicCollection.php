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
        return Characteristic::findOne($id);
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