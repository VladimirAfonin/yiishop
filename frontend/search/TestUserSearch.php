<?php
namespace frontend\search;

use frontend\entities\TestUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @property TestUser $username
 */
class TestUserSearch  extends Model
{
    public $username;

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function rules()
    {
        return [
            [['username'], 'safe'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TestUser::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'username', $this->username]);

        return $dataProvider;
    }
}