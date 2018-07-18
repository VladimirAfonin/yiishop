<?php
use common\rbac\Rbac;
use yii\helpers\Html;

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
    <?php if(Yii::$app->user->can(Rbac::MANAGE_PROFILE, ['user' => $model])): ?>
        <p class="pull-right">
            <?= Html::a('Profile', [''], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif; ?>
    <h1>
        <?= Html::encode($this->title) ?>
    </h1>
    <div class="panel panel-default">
        <div class="panel-body">
            <?= /** @var \frontend\entities\TestUser $model */
            Yii::$app->formatter->asNtext($model->desc) ?>
        </div>
    </div>
</div>
