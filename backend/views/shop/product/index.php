<?php
$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
use yii\helpers\Html;
use shop\entities\Shop\Product\Product;
use shop\helpers\PriceHelper;

?>
<div class="user-index">
    <p><?= Html::a('crate product', ['create'], ['class' => 'btn btn-success']) ?></p>
</div>
<div class="box">
    <div class="box-body">
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'value' => function (Product $model) {
                        return $model->mainPhoto ? Html::img($model->mainPhoto->getThumbFileUrl('file', 'admin')) : null;
                    },
                    'format' => 'html',
                    'contentOptions' => ['style' => 'width: 100px'],
                ],
                'id',
                [
                    'attribute' => 'name',
                    'value' => function(Product $model) {
                        return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'category_id',
                    'filter' => $searchModel->categoriesList(),
                    'value' => 'category.name'
                ],
                [
                    'attribute' => 'price_new',
                    'value' => function(Product $model) {
                        return PriceHelper::format($model->price_new);
                    },
                ],
            ]
        ]) ?>
    </div>
</div>

