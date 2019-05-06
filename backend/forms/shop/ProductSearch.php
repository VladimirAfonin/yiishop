<?php
namespace backend\forms\shop;

use shop\helpers\ProductHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use shop\entities\Shop\Product\Product;
use yii\helpers\ArrayHelper;
use shop\entities\Shop\Category;

class ProductSearch extends Model
{
    public $id;
    public $code;
    public $name;
    public $category_id;
    public $brand_id;
    public $status;

    public function rules()
    {
        return [
            [['id', 'category_id', 'brand_id', 'status'], 'integer'],
            [['code', 'name'], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function statusList()
    {
        return ProductHelper::statusList();
    }


    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Product::find()->with('mainPhoto', 'category');

        $datProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);
        $this->load($params);

        if ( ! $this->validate()) {
            $query->where('0=1');
            return $datProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
              ->andFilterWhere(['like', 'name', $this->name]);

        return $datProvider;
    }

    /**
     * @return array
     */
    public function categoriesList(): array
    {
        return ArrayHelper::map(
            Category::find()->andWhere(['>', 'depth', 0])->orderBy('lft')->asArray()->all(),
            'id',
            function(array $category) {
                return ($category['depth'] > 1 ? str_repeat('-- ', $category['depth'] - 1) . ' ' :  '') . $category['name'];
            }
        );
    }
}