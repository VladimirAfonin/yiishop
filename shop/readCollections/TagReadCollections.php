<?php
namespace shop\readCollections;


use shop\entities\Shop\Product\Tag;

class TagReadCollections
{
    public function find($id): ?Tag
    {
        return Tag::findOne($id);
    }
}