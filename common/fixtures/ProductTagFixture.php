<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class ProductTagFixture extends  ActiveFixture
{
    public $modelClass = 'shop\entities\ProductTag';
    public $dataFile = '@common/fixtures/data/product-tag.php';
}