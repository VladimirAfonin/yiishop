<?php
use yii\helpers\Html;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?=
        /** @var \frontend\entities\TestUser $model */
        Html::a(Html::encode($model->username), ['view', 'id' => $model->id]) ?>
    </div>
    <div class="panel-body">
        <?= Yii::$app->formatter->asNtext($model->desc) ?>
    </div>
</div>
