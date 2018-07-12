<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class TagFixture extends ActiveFixture
{
    public $modelClass = 'shop\entities\TestTags';
    public $dataFile = '@common/fixtures/data/tag.php';
}