<?php
/* @var \shop\entities\Shop\Category $category */
use backend\widgets\CategoriesWidget;
use backend\widgets\TagsWidget;
use yii\helpers\Html;

$this->title = $category->getSeoTitle();
$this->registerMetaTag(['name' => 'description', 'content' => $category->meta->description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $category->meta->description]);

$this->params['breadcrumbs'][] = ['label' => 'Catalog', 'url' => ['index']];
$this->params['breadcrumbs'][] = $category->name;
?>

<h1><?= Html::encode($category->getHeadingTitle()) ?></h1>
<?= $this->render('_subcategories', [
    'category' => $category
]) ?>
<?php if(trim($category->description)): ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?= Yii::$app->formatter->asNtext($category->description) ?>
        </div>
    </div>
<?php endif; ?>
<?= /** @var \yii\web\View $this */
$this->render('_list', compact('dataProvider')) ?>

<?php
$crumbs = [];
$requiredCategory = $category;
/** @var \shop\entities\Category $requiredCategory */
while($parentCategory = $requiredCategory->parent) {
    $crumbs[] = ['label' => $parentCategory->name, 'url' => ['category', 'id' => $parentCategory->id]];
}
$this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], $crumbs);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-index">
    <h1><?= Html::encode($this->title); ?></h1>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => "{items}\n{pager}",
        'itemView'     => '_item',
    ]) ?>
</div>