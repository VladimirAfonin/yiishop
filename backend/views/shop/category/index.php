<?php
use kartik\helpers\Html;
use yii\grid\GridView;
use shop\entities\Shop\Category;

/** @var \backend\forms\shop\CategorySearch $searchModel */


$this->title = 'Category';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <p>
        <?= Html::a('create category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="box">
        <div class="box-body">
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    [
                        'attribute' => 'name',
                        'value' => function(Category $model) {
                            $indent = ($model->depth > 1 ? str_repeat('&nbsp;&nbsp;', $model->depth - 1) . ' ' : '');
                            return $indent . Html::a(Html::encode($model->name), ['view', 'id' => $model->id]) ;
                        },
                        'format' => 'html'
                    ],
                    [
                        'value' => function(Category $model) {
                            return
                                   Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', ['move-up', 'id' => $model->id]/*, ['data-method' => 'post']*/) .
                                   Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', ['move-down', 'id' => $model->id]/*, ['data-method' => 'post']*/);
                        },
                        'format' => 'html',
                        'contentOptions' => ['style' => 'text-align: center'], // форматирование по центру
                    ],
                    'slug',
                    'title',
                    ['class' => \yii\grid\ActionColumn::class]
                ]
            ]) ?>
        </div>
    </div>
</div>
