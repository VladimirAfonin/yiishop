<?php
use yii\widgets\Menu;
/** @var array $items */
?>
<div class="panel panel-default">
    <div class="panel-heading">Categories</div>
        <?= Menu::widget([
            'options' => ['class' => 'nav nav-pills nav-stacked'],
            'items'   => $items,
            'submenuTemplate' => "\n   <ul class='nav nav-pills nav-stacked'>\n{items}\n</ul>\n",
        ]) ?>
</div>
