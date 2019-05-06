<?php
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;
$this->title = 'Cabinet';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cabinet-index">
    <h2><?= Html::encode($this->title) ?></h2>
    <p>hello!</p>

    <h2>Attach profile</h2>
    <?= AuthChoice::widget([
        'baseAuthUrl' => ['cabinet/network/attach']
    ]) ?>

</div>
