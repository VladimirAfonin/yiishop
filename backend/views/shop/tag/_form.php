<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="tag-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box box-default">
        <div class="box-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>
