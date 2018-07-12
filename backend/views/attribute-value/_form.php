<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use shop\entities\Product;
use shop\entities\Attribute;

/* @var $this yii\web\View */
/* @var $model shop\entities\AttributeValue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attribute-value-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_id')->dropDownList(Product::find()->select(['name','id'])->indexBy('id')->column(), ['prompt' => '']) ?>

    <?= $form->field($model, 'attribute_id')->dropDownList(Attribute::find()->select(['name', 'id'])->indexBy('id')->column(), ['prompt' => '']) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
