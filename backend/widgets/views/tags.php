<?php
use yii\bootstrap\Nav;
/** @var array $items */
?>

<div class="panel panel-default">
    <div class="panel-heading">Tags</div>

    <?= Nav::widget([
        'options' => ['class' => 'nav nav-pills nav-stacked'],
        'items'   => $items,
    ]) ?>
</div>
