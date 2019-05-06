<?php

namespace shop\forms\manage\Shop;

use shop\entities\Shop\Product\Modification;
use yii\base\Model;

class ModificationForm extends Model
{
    public $code;
    public $name;
    public $price;

    public function __construct(Modification $modification = null, array $config = [])
    {
        if($modification) {
            $this->code = $modification->code;
            $this->name = $modification->name;
            $this->price = $modification->price;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['price'], 'integer'],
        ];
    }
}