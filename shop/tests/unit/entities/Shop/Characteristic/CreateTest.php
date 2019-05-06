<?php
namespace shop\test\unit\entities\Shop\Characteristic;

use Codeception\Test\Unit;
use shop\entities\Shop\Characteristic;

class CreateTest extends Unit
{
    public function testSuccess()
    {
        $charact = Characteristic::create(
            $name = 'name',
            $type = Characteristic::TYPE_INTEGER,
            $required = true,
            $default = 0,
            $variants = [4,12],
            $sort = 15
        );

        $this->assertEquals($name, $charact->name);
        $this->assertEquals($type, $charact->type);
        $this->assertEquals($required, $charact->required);
        $this->assertEquals($default, $charact->default);
        $this->assertEquals($variants, $charact->variants);
        $this->assertEquals($sort, $charact->sort);
        $this->assertTrue($charact->isSelect());
    }
}