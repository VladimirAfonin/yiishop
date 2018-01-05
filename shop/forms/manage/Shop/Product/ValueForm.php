<?php
namespace shop\forms\manage\Shop\Product;

use yii\base\Model;
use shop\entities\Shop\Characteristic;
use shop\entities\Shop\Product\Value;

/*
 * форма для ввода значений
 *
 * */
class ValueForm extends Model
{
    public $value;
    private $_characteristic;

    public function __construct(Characteristic $characteristic = null, Value $value = null, array $config = [])
    {
        if($value) {
            $this->value = $value->value;
        }
        $this->_characteristic = $characteristic;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return array_filter([
            $this->_characteristic->required ? ['value', 'required'] : false,
            $this->_characteristic->isString() ? ['value', 'string', 'max' => 255] : false,
            $this->_characteristic->isInteger() ? ['value', 'integer'] : false,
            $this->_characteristic->isFloat() ? ['value', 'number'] : false,
            ['value', 'safe']
        ]);
    }

    public function attributesLabels(): array
    {
        return [
            'value' => $this->_characteristic->name,
        ];
    }

    public function getId(): int
    {
        return $this->_characteristic->id;
    }
}