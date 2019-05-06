<?php
/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginContent('@frontend/views/layouts/main.php') ?>

    <div class="row test">
        <div id="content" class="col-sm-12">
            test 1
            <?= $content ?>
            test 2
        </div>
    </div>

<?php $this->endContent() ?>