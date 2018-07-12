<?php

namespace shop\entities\query;

/**
 * This is the ActiveQuery class for [[\shop\entities\Product]].
 *
 * @see \shop\entities\Product
 */
class ProductQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \shop\entities\Product[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \shop\entities\Product|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
