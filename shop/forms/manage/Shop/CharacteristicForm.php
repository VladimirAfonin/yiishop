<?php
namespace shop\forms\manage\Shop;

use shop\entities\Shop\Characteristic;
use yii\base\Model;
use shop\helpers\CharacteristicHelper;

class CharacteristicForm extends Model
{
    public $name;
    public $type;
    public $required;
    public $default;
    public $textVariants;
    public $sort;

    private$_charactObj;

    public function __construct(Characteristic $characteristic = null, array $config = [])
    {
        if($characteristic) {
//            $this->setAttributes($this->getAttributes()); // без перечисления
            $this->name = $characteristic->name;
            $this->type = $characteristic->type;
            $this->required = $characteristic->required;
            $this->default = $characteristic->default;
            $this->textVariants = implode(PHP_EOL,$characteristic->variants);
            $this->sort = $characteristic->sort;

            $this->_charactObj = $characteristic;
        } else {
            $this->sort = Characteristic::find()->max('sort') + 1;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name', 'type', 'sort'], 'required'],
            [['required'], 'boolean'],
            [['default'], 'string', 'max' => 255],
            [['textVariants'], 'string'],
            [['sort'], 'integer'],
            [['name'], 'unique', 'targetClass' => Characteristic::class, 'filter' => $this->_charactObj ? ['<>', 'id', $this->_charactObj->id] : null ]
        ];
    }

    /**
     * get array of 'variants',
     * from text to array
     *
     * @return array
     */
    public function getVariants(): array
    {
        return preg_split('#[\r\n]+#i', $this->textVariants);
    }

    /**
     *
     * @return array
     */
    public function typesList(): array
    {
        return CharacteristicHelper::typeList();
    }
}