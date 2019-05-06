<?php
$this->title = 'Create Category';
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-create">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
</div>
