<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Email - Send'; ?>
<div class="site-contact">
    <h1><?= Html::encode($this->title)?></h1>
    <p>Please, fill the form below:</p>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'subject') ?>
            <?= $form->field($model, 'to') ?>
            <?= $form->field($model, 'reply_to')->textInput(['value' => Yii::$app->user->identity->email]) ?>
            <?= $form->field($model, 'cc') ?>
            <?= $form->field($model, 'bcc') ?>
            <?= Html::tag('label', 'Email template', ['class' => 'control-label']) // todo ?>
            <?= Html::dropDownList('drop_list', null, ['A'=>1, 'B'=>2, 'C'=>3], ['prompt' => 'Choose template...', 'class' => 'form-control template-email']) // todo?>
            <?= $form->field($model, 'desc')->textarea(['rows' => 4, 'class' => 'form-control template-txt-body']) ?>
            <?= Html::tag('div',
                    Html::tag('a', 'B', ['data-action' => 'b', 'href' => 'javascript:void(0)']) . '&nbsp;|&nbsp;' .
                    Html::tag('a', 'I', ['data-action' => 'i', 'href' => 'javascript:void(0)']) . '&nbsp;|&nbsp;' .
                    Html::tag('a', 'U', ['data-action' => 'u', 'href' => 'javascript:void(0)']) . '&nbsp;|&nbsp;' .
                    Html::tag('a', 'P', ['data-action' => 'p', 'href' => 'javascript:void(0)']),
                ['id' => 'controls'])
            ?>
            <?= $form->field($model, 'html')->textarea(['rows' => 10, 'class' => 'form-control template-html-body', 'id' => 'comment']) ?>
            <div class="form-group">
                <?= Html::submitButton('submit', ['class' => 'btn btn-success', 'name' => 'email-button']); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$js = <<< JS
    var template_text_body_A = 'template A some text here body';
    var template_html_body_A = "template A some html here body";
    var template_text_body_B = 'template B some text here body';
    var template_html_body_B = 'template B some html here body';
    var template_text_body_C = 'template C some text here body';
    var template_html_body_C = 'template C some text here body';

    $('.template-email').change(function() {
        val = $(this).val();
        $('.template-txt-body, .template-html-body').empty();
        if(val == 'A') {
            $('.template-txt-body').append(template_text_body_A);
            $('.template-html-body').append(template_html_body_A);
        } else if(val == 'B') {
            $('.template-txt-body').append(template_text_body_B);
            $('.template-html-body').append(template_html_body_B);
        } else if(val == 'C') {
        $('.template-txt-body').append(template_text_body_C);
        $('.template-html-body').append(template_html_body_C);
        }
    });

    (function(){
	var controlsWrap = document.getElementById('controls'),
  		controls = controlsWrap.getElementsByTagName('a'),
        textarea = document.getElementById('comment');

    Array.prototype.forEach.call(controls, function(control){
        control.addEventListener('click', function(){
            var tag = control.getAttribute('data-action') || "";

            wrapWithTag(tag);
        });
    });

  function wrapWithTag(tag){
  	tag = tag || "";
  	var allowedTags = ['b', 'i', 'u', 'p'];
    if (allowedTags.indexOf(tag) < 0) {
    	return false;
    }
    var oldValue = textarea.value,
    		openTag = '<' + tag + '>',
        closeTag = '</' + tag + '>',
        start = textarea.selectionStart,
        end = textarea.selectionEnd;
    if (start >= oldValue.length) {
    	return false;
    }
    var newValue = oldValue.substr(0, start) + openTag + oldValue.substr(start, end - start) + closeTag + oldValue.substr(end);
    textarea.value = newValue;
  }
})();


JS;
$this->registerJs($js); ?>