<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class AttributeFixture extends ActiveFixture
{
    public $modelClass = 'shop\entities\Attribute';
    public $dataFile = '@common/fixtures/data/attribute.php';
}