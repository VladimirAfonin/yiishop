<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model shop\entities\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category_id',
            'name',
            'content:ntext',
            'price',
            'active:boolean',
//            [
//                'attribute' => 'active',
//                'filter'    => [0 => 'Нет', 1 => 'Да'],
//                'format'    => 'boolean',
//            ],
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider(['query' => $model->getAttributeValue()->with(['productAttribute'])]), // product->getValues() ... relation method
        'columns'      => [
//                'product_id',
            [
                'attribute' => 'attribute_id',
                'value'     => 'productAttribute.name', // ->productAttribute->name
            ],
            'value',
            [
                'class'      => 'yii\grid\ActionColumn',
                'controller' => 'admin/attribute-value',
            ]
        ]
    ]); ?>


</div>
