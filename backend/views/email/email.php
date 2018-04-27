<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Email */
/* @var $form ActiveForm */
?>
<div class="backend-views-email">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'discovery') ?>
        <?= $form->field($model, 'account_id') ?>
        <?= $form->field($model, 'recipient_id') ?>
        <?= $form->field($model, 'author_id') ?>
        <?= $form->field($model, 'cc') ?>
        <?= $form->field($model, 'bcc') ?>
        <?= $form->field($model, 'desc') ?>
        <?= $form->field($model, 'html') ?>
        <?= $form->field($model, 'source') ?>
        <?= $form->field($model, 'storage_url') ?>
        <?= $form->field($model, 'created_at') ?>
        <?= $form->field($model, 'code') ?>
        <?= $form->field($model, 'to') ?>
        <?= $form->field($model, 'from') ?>
        <?= $form->field($model, 'reply_to') ?>
        <?= $form->field($model, 'subject') ?>
        <?= $form->field($model, 'language_id') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- backend-views-email -->
