<?php

use yii\helpers\Html;
use yii\grid\GridView;
use shop\entities\User;
use shop\helpers\UserHelper;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\forms\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

<!--    <h1>--><?//= Html::encode($this->title) ?><!--</h1>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'created_at',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_from',
                    'attribute2' => 'date_to',
                    'type' => DatePicker::TYPE_RANGE,
                    'separator' => '-',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ],
                ]),
                'format' => 'datetime',
            ],
            [
                'attribute' => 'username',
                'value' => function(User $user) {
                    return Html::a(Html::encode($user->username), ['view', 'id' => $user->id]);
                },
                'format' => 'html'
            ],
            'email:email',
            [
                'attribute' => 'status',
                'value' => function(User $user) {
                    return UserHelper::statusLabel($user->status);
                },
                'format' => 'html',
                'filter' => UserHelper::statusList() // выпадающий список
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
