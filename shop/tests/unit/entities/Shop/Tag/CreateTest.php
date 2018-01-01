<?php

namespace shop\test\unit\entities\Shop\Tag;

use Codeception\Test\Unit;
use shop\entities\Shop\Tag;

class TagCreateTest extends Unit
{
    public function testSuccess()
    {
        $tag = Tag::create(
            $name = 'Name',
            $slug = 'slug'
        );

        $this->assertEquals($name, $tag->name);
        $this->assertEquals($slug, $tag->slug);
    }
}