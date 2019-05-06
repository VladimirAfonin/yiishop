<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class AttributeValueFixture extends ActiveFixture
{
    public $modelClass = 'shop\entities\AttributeValue';
    public $dataFile = '@common/fixtures/data/attribute-value.php';
}