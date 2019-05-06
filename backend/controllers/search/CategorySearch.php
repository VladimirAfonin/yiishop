<?php

namespace backend\controllers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use shop\entities\Category;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * CategorySearch represents the model behind the search form of `shop\entities\Category`.
 */
class CategorySearch extends Category
{
    public $grand_id;
    public $products_count;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id','products_count'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Category::find()
//            ->from('category c')
            ->select(['category.*', 'products_count' => new Expression('COUNT(product.id)')])
            ->joinWith(['products'], false)
            ->groupBy('category.id')
            ->with(['parent'])
//            ->from(['t' => Category::tableName()])
//            ->joinWith([
//                'parent' => function(ActiveQuery $query) { $query->from(['parent' => Category::tableName()]); },
//                'parent.parent' => function(ActiveQuery $query) { $query->from(['grandpa' => Category::tableName()]); },
//            ])
        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'name' => [
                        'ASC' => ['{{%category}}.name' => SORT_ASC],
                        'DESC' => ['{{%category}}.name' => SORT_DESC],
                    ],
                    'parent_id',
                    'products_count',
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
//            'parent.parent_id' => $this->grand_id,
//            'grandpa.status' => $this->grand_id,
        ]);

        // check if we have own dynamic attribute ...
        if (isset($this->products_count)) {
            $query->andHaving(['products_count' => $this->products_count]);
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
