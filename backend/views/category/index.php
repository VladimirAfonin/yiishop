<?php

use backend\widgets\CategoriesWidget;
use backend\widgets\TagsWidget;
use yii\helpers\Html;
use yii\grid\GridView;
use shop\entities\Category;

/* @var $this yii\web\View */
/* @var $searchModel backend\controllers\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Category', ['admin/category/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
//            'parent_id',
            [
                'attribute' => 'parent_id',
                'filter'    => Category::find()->select(['name', 'id'])->indexBy('id')->column(),
                'value' => function(Category $category) {
                    return \yii\helpers\ArrayHelper::getValue($category, 'parent.name'); // $category->parent->name
                },
            ],
            [
                'label' => 'Product count',
                'attribute' => 'products_count',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<hr>
<?= CategoriesWidget::widget([
    'category' => isset($this->params['category']) ? $this->params['category'] : null,
]) ?>

<?= TagsWidget::widget([
    'tag' => isset($this->params['tag']) ? $this->params['tag'] : null,
]) ?>