<?php

use shop\entities\TestTags;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use shop\entities\Category;

/* @var $this yii\web\View */
/* @var $model shop\entities\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'category_id')->dropDownList(Category::find()->select(['name', 'id'])->indexBy('id')->column(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'price')->textInput() ?>

            <?= $form->field($model, 'active')->textInput() ?>

            <?= $form->field($model, 'tagsArray')->checkboxList(TestTags::find()->select(['name', 'id'])->indexBy('id')->column()) ?>
        </div>
    </div>
    <div class="col-md-6">
        <?php /** @var \shop\entities\AttributeValue[] $values */
        foreach($values as $value): ?>
            <?= $form->field($value, '[' . $value->productAttribute->id . ']value')->label($value->productAttribute->name); ?>
        <?php endforeach; ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
