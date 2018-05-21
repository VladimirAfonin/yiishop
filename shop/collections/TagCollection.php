<?php
namespace shop\collections;

use shop\entities\Shop\Tag;

class TagCollection
{
    /**
     * @param $id
     * @return Tag
     */
    public function get($id): Tag
    {
        if(!$tag = Tag::findOne($id)) throw new NotFoundException('tag not found.');
        return $tag;
    }

    /**
     * @param Tag $tag
     */
    public function save(Tag $tag)
    {
        if(!$tag->save()) { throw new NotFoundException('saving error.'); }
    }

    /**
     * @param Tag $tag
     */
    public function remove(Tag $tag)
    {
        if(!$tag->delete()) { throw new NotFoundException('delete error.'); }
    }

    /**
     * @param $tagName
     * @return Tag
     */
    public function findByName($tagName): ?Tag
    {
       return Tag::findOne(['name' => $tagName]);
    }
}