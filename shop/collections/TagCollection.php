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
        return Tag::findOne($id);
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
}