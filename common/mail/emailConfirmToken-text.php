<?php
use yii\helpers\Html;
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm', 'token' => $user->email_confirm_token]);
?>

Hello <?= Html::encode($user->username) ?>,
Follow the link below to confirm your email:<?= Html::a( Html::encode( $confirmLink ), $confirmLink ) ?>

