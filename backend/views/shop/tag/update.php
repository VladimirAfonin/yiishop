<?php
$this->title = 'Update Brand' . $tag->name;
$this->params['breadcrumbs'][] = ['label' => 'Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $tag->name, 'url' => ['view', 'id' => $tag->id]];
$this->params['breadcrumbs'][] = 'update';
?>
<div class="tag-update">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
</div>
