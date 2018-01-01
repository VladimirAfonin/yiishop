<?php
namespace shop\services\manage\Shop;

use shop\collections\TagCollection;
use shop\entities\Shop\Tag;
use shop\forms\manage\Shop\TagForm;
use yii\helpers\Inflector;

class TagManageService
{
    public $tagCollect;
    
    public function __construct(TagCollection $tagCollect)
    {
        $this->tagCollect = $tagCollect;
    }

    /**
     * @param TagForm $form
     * @return Tag
     */
    public function create(TagForm $form): Tag
    {
        $tag = Tag::create(
            $form->name,
            $form->slug ?: Inflector::slug($form->name)
        );
        $this->tagCollect->save($tag);
        return $tag;
    }

    /**
     * @param $id
     * @param TagForm $form
     */
    public function edit($id, TagForm $form): void
    {
        $tag = $this->tagCollect->get($id);
        $tag->edit(
            $form->name,
            $form->slug ?: Inflector::slug($form->name)
        );
        $this->tagCollect->save($tag);
    }

    /**
     * @param $id
     */
    public function remove($id): void
    {
        $tag = $this->tagCollect->get($id);
        $this->tagCollect->remove($tag);
    }
    
}