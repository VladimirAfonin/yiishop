<?php
namespace shop\collections;

use SebastianBergmann\CodeCoverage\RuntimeException;
use shop\entities\Shop\Category;

class CategoryCollection
{
    /**
     * @param $id
     * @return Category
     */
    public function get($id): Category
    {
        if(!$category = Category::findOne($id)) { throw new \RuntimeException('category not found'); }
        return $category;
    }

    /**
     * @param Category $category
     */
    public function save(Category $category)
    {
        if(!$category->save()) { throw new NotFoundException('saving error.'); }
    }

    /**
     * @param Category $category
     */
    public function remove(Category $category)
    {
        if(!$category->delete()) { throw new NotFoundException('delete error.'); }
    }
}