<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use \yii\widgets\ListView;

$this->title = 'TestUsers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= /** @var \frontend\search\TestUserSearch $searchModel */
    $this->render('_search', ['model' => $searchModel]); ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions'  => ['class' => 'item'],
        'itemView'     => '_item',
    ]) ?>
</div>
