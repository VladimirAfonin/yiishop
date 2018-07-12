<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model shop\entities\TestTags */

$this->title = 'Update Test Tags: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Test Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="test-tags-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
