<?php
use \yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="panel panel-default">
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?=
        /** @var \frontend\search\TestUserSearch $model */
        $form->field($model, 'username') ?>
        <div>
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Reset', [''], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
