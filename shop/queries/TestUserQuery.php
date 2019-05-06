<?php
namespace shop\queries;

use yii\db\ActiveQuery;

class TestUserQuery extends ActiveQuery
{
    public function action($alias)
    {
        return $this->andWhere([
            ($alias ? $alias . '.' : '') . 'status' => Product::STATUS_ACTIVE
        ]);
    }
}