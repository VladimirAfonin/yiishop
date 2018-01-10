<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $tag->name;
$this->params['breadcrumbs'][] = ['label' => 'tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <p>
        <?= Html::a('update', ['update', 'id' => $tag->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('delete', ['delete', 'id' => $tag->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'are you sure you want to delete this item?',
                'method' => 'post'
            ]
        ]) ?>
    </p>
    <div class="box">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $tag,
                'attributes' => ['id', 'name', 'slug']
            ]) ?>
        </div>
    </div>
</div>
