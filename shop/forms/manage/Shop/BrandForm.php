<?php
namespace shop\forms\manage\Shop;

use shop\forms\CompositeForm;
use shop\forms\manage\MetaForm;
use yii\base\Model;
use shop\entities\Shop\Brand;
use yii\helpers\ArrayHelper;

/**
 * @property MetaForm $meta;
 */
class BrandForm extends CompositeForm
{
    public $name;
    public $slug;

    /* Brand */
    private $_brandObj;

    /**
     * BrandForm constructor.
     * @param Brand|null $brand
     * @param array $config
     */
    public function __construct(Brand $brand = null, $config = [])
    {
        if($brand) {
            $this->name = $brand->name;
            $this->slug = $brand->slug;
            $this->_brandObj = $brand;
            $this->meta = new MetaForm($brand->meta);
        } else {
            $this->meta = new MetaForm();
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

    /**
     * @return array
     */
    protected function internalForms(): array
    {
        return [
          'meta',
        ];
    }
}