<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class ProductFixture extends  ActiveFixture
{
    public $modelClass = 'shop\entities\Product';
    public $dataFile = '@common/fixtures/data/product.php';
}