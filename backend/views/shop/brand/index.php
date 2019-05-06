<?php
use kartik\helpers\Html;
use yii\grid\GridView;
use shop\entities\Shop\Brand;

$this->title = 'Brands';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <p>
        <?= Html::a('create brand', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    [
                        'attribute' => 'name',
                        'value' => function(Brand $model) {
                            return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                        },
                        'format' => 'html'
                    ],
                    'slug',
                    ['class' => \yii\grid\ActionColumn::class]
                ]
            ]) ?>
        </div>
    </div>
</div>
