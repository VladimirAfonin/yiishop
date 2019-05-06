<?php
namespace shop\services\manage\Shop;

use shop\collections\CategoryCollection;
use shop\collections\ProductCollection;
use shop\forms\manage\Shop\CategoryForm;
use shop\entities\Shop\Category;
use shop\entities\Meta;

class CategoryManageService
{
    /* Коллекция категорий */
    private $_categoriesCollect;
    private $_productsCollection;

    public function __construct(CategoryCollection $catCollect, ProductCollection $productsCollection)
    {
        $this->_categoriesCollect = $catCollect;
        $this->_productsCollection = $productsCollection;
    }

    /**
     * @param CategoryForm $form
     * @return Category
     */
    public function create(CategoryForm $form): Category
    {
        $parent = $this->_categoriesCollect->get($form->parentId);
        $category = Category::create(
            $form->name,
            $form->slug,
            $form->title,
            $form->description,
            new Meta( $form->meta->title, $form->meta->description, $form->meta->keywords )
        );
        $category->appendTo($parent);
        $this->_categoriesCollect->save($category);
        return $category;
    }

    /**
     * @param $id
     * @param CategoryForm $form
     */
    public function edit($id, CategoryForm $form): void
    {
        $category = $this->_categoriesCollect->get($id);
        $this->assertIsNotRoot($category);
        $category->edit(
            $form->name,
            $form->slug,
            $form->title,
            $form->description,
            new Meta( $form->meta->title, $form->meta->description, $form->meta->keywords )
        );
        if($form->parentId !== $category->parent->id) {
            $parent = $this->_categoriesCollect->get($form->parentId);
            $category->appendTo($parent);
        }
        $this->_categoriesCollect->save($category);
    }

    /**
     * @param $id
     */
    public function remove($id): void
    {
        $category = $this->_categoriesCollect->get($id);
        $this->assertIsNotRoot($category);
        if($this->_productsCollection->existsByMainCategory($category->id)) {
            throw new \RuntimeException('unable to remove category with products.');
        }
        $this->_categoriesCollect->remove($category);
    }

    /**
     * не родительская ли категория
     *
     * @param Category $category
     */
    private function assertIsNotRoot(Category $category)
    {
        if($category->isRoot()) {
            throw new \RuntimeException('unable to manage the root category.');
        }
    }

    /**
     * @param $id
     */
    public function moveUp($id): void
    {
        $category = $this->_categoriesCollect->get($id);
        $this->assertIsNotRoot($category);
        if ($prev = $category->prev) { // nestedSetbehavior action
            $category->insertBefore($prev); // nestedSetbehavior action
        }
        $this->_categoriesCollect->save($category);
    }

    /**
     * @param $id
     */
    public function moveDown($id): void
    {
        $category = $this->_categoriesCollect->get($id);
        $this->assertIsNotRoot($category);
        if ($next = $category->next ){ // nestedSetbehavior action
            $category->insertAfter($next); // nestedSetbehavior action
        }
        $this->_categoriesCollect->save($category);
    }

}