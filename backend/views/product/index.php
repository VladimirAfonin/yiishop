<?php

use shop\entities\Product;
use yii\helpers\Html;
use yii\grid\GridView;
use shop\entities\Category;

/* @var $this yii\web\View */
/* @var $searchModel backend\controllers\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Product', ['admin/product/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'category_id',
                'filter'    => Category::find()->select(['name', 'id'])->indexby('id')->column(),
                'value' => 'category.name' // function(Category $category) { return ArrayHelper::getValue($category, 'name'); }
            ],
//            'category_id',
            'name',
            'content:ntext',
            'price',
            //'active',
            [
                'attribute' => 'active',
                'filter'    => [0 => 'Нет', 1 => 'Да'],
                'format'    => 'boolean',
            ],
            [
                'attribute' => 'tag_id',
                'label' => 'Tags',
                'filter' => \shop\entities\TestTags::find()->select(['name', 'id'])->indexBy('id')->column(),
                'value' => function(Product $product) {
                    return implode(', ', \yii\helpers\ArrayHelper::map($product->tags, 'id', 'name'));
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'admin/product',
            ],
        ],
    ]); ?>
</div>
