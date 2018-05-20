<?php
namespace shop\forms\manage\Shop\Product;

use shop\entities\Shop\Characteristic;
use shop\forms\CompositeForm;
use shop\forms\manage\MetaForm;
use shop\entities\Shop\Product\Product;
use shop\entities\Shop\Brand;
use yii\helpers\ArrayHelper;

/* @property ValueForm[] $values
 * @property PriceForm price
 * @property MetaForm meta
 * @property CategoriesForm categories
 * @property PhotosForm photos
 * @property TagsForm tags
 */
class ProductCreateForm extends CompositeForm
{
    public $brandId;
    public $code;
    public $name;

    public function __construct(array $config = [])
    {
        $this->price = new PriceForm();
        $this->meta = new MetaForm();
        $this->categories = new CategoriesForm();
        $this->photos = new PhotosForm();
        $this->tags = new TagsForm();

        $this->values = array_map(function(Characteristic $charact) { // массив из 'ValueForm'
            return new ValueForm($charact);
        }, Characteristic::find()->orderBy('sort')->all()); // берем все характ на нашем сайте

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['brandId', 'code', 'name'], 'required'],
            [['code', 'name'], 'string', 'max' => 255],
            [['brandId'], 'integer'],
            [['code'], 'unique', 'targetClass' => Product::class]
        ];
    }

    protected function internalForms(): array
    {
        return ['price', 'meta', 'categories', 'photos', 'tags', 'values'];
    }

    /**
     * @return array
     */
    public function brandsList(): array
    {
        return ArrayHelper::map(Brand::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }
}