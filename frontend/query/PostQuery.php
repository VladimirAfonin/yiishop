<?php
namespace frontend\query;


use yii\db\ActiveQuery;

class PostQuery extends ActiveQuery
{
    /**
     * @param $userId
     * @return $this
     */
    public function forUser($userId)
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * @param $limit
     * @return $this
     */
    public function latest($limit)
    {
        return $this->limit($limit)->orderBy(['id' => SORT_DESC]);
    }

    /**
     * @param null $db
     * @return array|\yii\db\ActiveRecord[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return array|null|\yii\db\ActiveRecord
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}