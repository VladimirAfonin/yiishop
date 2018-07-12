<?php
namespace backend\widgets;

use shop\entities\Category;
use Yii;
use yii\base\Widget;

class CategoriesWidget extends Widget
{
    /** @var Category */
    public $category;

    public function run()
    {
        $cacheKey = ['categories-widget-items', 'id' => $this->category ? $this->category->id : null];
        if (!$items = Yii::$app->cache->get($cacheKey)) {
            $categories = Category::find()->orderBy('name')->all();
            $items = $this->getItemsRecursive($categories, null, $this->category);
            Yii::$app->cache->set($cacheKey, $items);
        }
       return $this->render('categories', ['items' => $items]);
    }

    /**
     * @param Category[] $categories
     * @param integer $parentId
     * @param Category $current
     * @return array
     */
    public function getItemsRecursive(&$categories, $parentId, $current)
    {
        $items = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $items[] = [
                    'label'  => $category->name,
                    'url'    => ['/admin/category', 'id' => $category->id],
                    'active' => $current && $this->category->id == $current->id ? true : null,
                    'items'  => $this->getItemsRecursive($categories, $category->id, $current),
                ];
            }
        }
        return $items;
    }
}