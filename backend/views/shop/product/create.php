<?php
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Html;
?>
<?= Html::csrfMetaTags() ?>
<div class="product-create">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
    ]) ?>
    <div class="box box-default">
        <div class="box box-header">Common</div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'brandId')->dropDownList($model->brandsList()) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <?= $form->field($model, 'description')->textarea(['rows' => 10]) ?>
        </div>
    </div>

</div>
<div class="box box-default">
    <div class="box-header with-border">Price</div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model->price, 'new')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model->price, 'old')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">Categories</div>
            <div class="box-body">
                <?= $form->field($model->categories, 'main')->dropDownList($model->categories->categoriesList(), ['prompt' => '-- выберите']) ?>
                <?= $form->field($model->categories, 'others')->checkboxList($model->categories->categoriesList()) ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">Tags</div>
            <div class="box-body">
                <?= $form->field($model->tags, 'existing')->checkboxList($model->tags->tagsList()) ?>
                <?= $form->field($model->tags, 'textNew')->textInput() ?>
            </div>
        </div>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">Characteristics</div>
    <div class="box-body">
        <?php foreach($model->values as $k => $value): ?>
            <?php if($variants = $value->variantsList()): ?>
                <?= $form->field($value, '[' . $k . ']value')->dropDownList($variants,['prompt' => '']) ?>
            <?php else: ?>
                <?= $form->field($value, '[' . $k . ']value')->textInput() ?>
            <?php endif ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        Photos
    </div>
    <div class="box-body">
        <?= $form->field($model->photos, 'files[]')->widget(FileInput::class,[
            'options' => [
                'accept' => 'image/*',
                'multiple' => true,
            ]
        ] ) ?>
    </div>

    <div class="box box-default">
        <div class="box-header with-border">
            SEO
        </div>
        <div class="box-body">
            <?= $form->field($model->meta, 'title')->textInput() ?>
            <?= $form->field($model->meta, 'description')->textarea(['rows' => 2]) ?>
            <?= $form->field($model->meta, 'keywords')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end()?>
</div>
