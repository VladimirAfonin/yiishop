<?php
$this->title = 'Create Tag';
$this->params['breadcrumbs'][] = ['label' => 'Tag', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tag-create">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
</div>
