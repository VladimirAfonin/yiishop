<?php
namespace shop\services\manage\Shop;

use shop\collections\CharacteristicCollection;
use shop\entities\Shop\Characteristic;
use shop\forms\manage\Shop\CharacteristicForm;

class CharacteristicManageService
{
    private $_charactCollect;

    public function __construct(CharacteristicCollection $charactCollect)
    {
        $this->_charactCollect = $charactCollect;
    }

    /**
     * @param CharacteristicForm $form
     * @return Characteristic
     */
    public function create(CharacteristicForm $form): Characteristic
    {
        $charact = Characteristic::create(
            $form->name,
            $form->type,
            $form->required,
            $form->default,
            $form->variants,
            $form->sort
        );
        $this->_charactCollect->save($charact);
        return $charact;
    }

    /**
     * @param $id
     * @param CharacteristicForm $form
     */
    public function edit($id, CharacteristicForm $form): void
    {
        $charact = $this->_charactCollect->get($id);
        $charact->edit(
            $form->name,
            $form->type,
            $form->required,
            $form->default,
            $form->variants,
            $form->sort
        );
        $this->_charactCollect->save($charact);
    }

    /**
     * @param $id
     */
    public function remove($id): void
    {
        $charact = $this->_charactCollect->get($id);
        $this->_charactCollect->remove($charact);
    }
}