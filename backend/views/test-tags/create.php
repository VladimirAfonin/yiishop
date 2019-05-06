<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model shop\entities\TestTags */

$this->title = 'Create Test Tags';
$this->params['breadcrumbs'][] = ['label' => 'Test Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-tags-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
