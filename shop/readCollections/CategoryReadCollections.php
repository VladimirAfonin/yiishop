<?php
namespace shop\readCollections;

use shop\entities\Shop\Category;

class CategoryReadCollections
{
    public function getRoot(): Category
    {
        return Category::find()->roots()->one();
    }

    public function find($id): ?Category
    {
        return Category::find()->andWhere(['id' => $id])->andWhere(['>', 'depth', 0])->one();
    }
}