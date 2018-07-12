<?php
namespace backend\widgets;

use shop\entities\TestTags;
use Yii;
use yii\base\Widget;

class TagsWidget extends Widget
{
    /** @var  TestTags $tag */
    public $tag;

    public function run()
    {
        $tags = TestTags::getDb()->cache(function() {
            return TestTags::find()->orderBy('name')->all();
        }, 3600);

        $cacheKey = ['tags-widget-items', 'id' => $this->tag ? $this->tag->id : null];

        if (!$items = Yii::$app->cache->get($cacheKey)) {
            $items = [];
            foreach ($tags /*TestTags::find()->orderBy('name')->each()*/ as $tag) {
                $items[] = [
                    'label'  => $tag->name,
                    'url'    => ['/admin/catalog/test-tags'],
                    'active' => $this->tag && $tag->id == $this->tag->id ? true : null,
                ];
            }
            Yii::$app->cache->set($cacheKey, $items);
        }

        return $this->render('tags', ['items' => $items]);
    }
}