<?php
$this->title = 'Update Category' . $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['view', 'id' => $category->id]];
$this->params['breadcrumbs'][] = 'update';
?>
<div class="category-update">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
</div>
