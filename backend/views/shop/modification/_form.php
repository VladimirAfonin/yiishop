<?php
use yii\widgets\ActiveForm;
use kartik\helpers\Html;
?>

<div class="modification-form">
    <?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton('save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>


