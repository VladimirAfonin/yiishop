<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = 'Catalog';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?=  $this->render('_subcategories', compact('category')) ?>
<?=  $this->render('_list', compact('dataProvider')) ?>

