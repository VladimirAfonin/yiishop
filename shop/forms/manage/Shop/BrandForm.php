<?php
namespace shop\forms\manage\Shop;

use yii\base\Model;
use shop\entities\Shop\Brand;

class BrandForm extends Model
{
    public $name;
    public $slug;
    private $_brandObj;

    public function __construct(Brand $brand = null, $config = [])
    {
        if($brand) {
            $this->name = $brand->name;
            $this->slug = $brand->slug;
            $this->_brandObj = $brand;
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
            ['slug', 'match', 'pattern' => '#^[a-z0-9_-]*$s'],
            [['name', 'slug'], 'unique', 'targetClass' => Brand::class, 'filter' => ['<>', 'id', $this->_brandObj->id]]
        ];
    }
}