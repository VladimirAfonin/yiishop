<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class CategoryFixture extends ActiveFixture
{
    public $modelClass = 'shop\entities\Category';
    public $dataFile = '@common/fixtures/data/category.php';
}