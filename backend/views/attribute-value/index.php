<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\controllers\search\AttributeValueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attribute Values';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-value-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Attribute Value', ['admin/attribute-value/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'product_id',
//            'attribute_id',
            [
                'attribute' => 'attribute_id',
                'filter'    => \shop\entities\Attribute::find()->select(['name', 'id'])->indexBy('id')->column(),
            ],
            'value',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
