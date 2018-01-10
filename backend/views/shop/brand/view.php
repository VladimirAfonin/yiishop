<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $brand->name;
$this->params['breadcrumbs'][] = ['label' => 'brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <p>
        <?= Html::a('update', ['update', 'id' => $brand->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('delete', ['delete', 'id' => $brand->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'are you sure you want to delete this item?',
                'method' => 'post'
            ]
        ]) ?>
    </p>
    <div class="box">
        <div class="box-header with-border">Common</div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $brand,
                'attributes' => ['id', 'name', 'slug']
            ]) ?>
        </div>
    </div>
    <div class="box">
        <div class="box-header with-border">SEO</div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $brand,
                'attributes' => ['meta.title', 'meta.description', 'meta.keywords']
            ]) ?>
        </div>
    </div>
</div>
