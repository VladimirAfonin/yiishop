<?php
namespace shop\test\unit\entities\Shop\Brand;

use Codeception\Test\Unit;
use shop\entities\Shop\Brand;
use shop\entities\Meta;

class CreateTest
{
    public function testSuccess()
    {
        $brand = Brand::create(
            $name = 'Name',
            $slug = 'slug',
            $meta = new Meta('title', 'description', 'keywords')
        );

        $this->assertEquals($name, $brand->name);
        $this->assertEquals($slug, $brand->slug);
        $this->assertEquals($meta, $brand->meta);
    }
}
