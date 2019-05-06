<?php
namespace shop\forms\manage\Shop\Product;

use yii\base\Model;
use shop\entities\Shop\Product\Product;
use yii\helpers\ArrayHelper;
use shop\entities\Shop\Category;

class CategoriesForm extends Model
{
    public $main;
    public $others = [];

    public function __construct(Product $product = null, array $config = [])
    {
        if($product) {
            $this->main = $product->category_id;
            $this->others = ArrayHelper::getColumn($product->categoryAssignments, 'category_id'); // по связи получаем категории
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['main', 'required'],
            ['main', 'integer'],
            ['others', 'each', 'rule' => ['integer']]
        ];
    }

    /**
     * @return array
     */
    public function categoriesList(): array
    {
        return ArrayHelper::map(Category::find()->andWhere(['>', 'depth', 0])->orderBy('lft')->asArray()->all(), 'id', function (array $category) {
            return ($category['depth'] > 1 ? str_repeat('-- ', $category['depth'] - 1) . ' ' : '') . $category['name'];
        });
    }


}