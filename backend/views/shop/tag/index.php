<?php
use kartik\helpers\Html;
use yii\grid\GridView;
use shop\entities\Shop\Tag;

$this->title = 'Tag';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <p>
        <?= Html::a('create tag', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'value' => function(Tag $model) {
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
