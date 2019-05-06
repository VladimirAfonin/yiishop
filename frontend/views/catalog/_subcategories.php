<?php
use yii\helpers\{Html,Url};
?>
<?php /** @var \shop\entities\Shop\Category $category */
if (($category->children)) : ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php foreach ($category->children as $child) : ?>
                <a href="<? Html::encode(Url::to(['category', 'id' => $child->id])) ?>"><?= Html::encode($child->name) ?></a>&nbsp;
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>