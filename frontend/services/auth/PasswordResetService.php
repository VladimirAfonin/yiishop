<?php

namespace frontend\services\auth;

use frontend\forms\PasswordResetRequestForm;
use common\entities\User;
use frontend\forms\ResetPasswordForm;

class PasswordResetService
{
    private $supportEmail;

    public function __construct($supportEmail)
    {
        $this->supportEmail = $supportEmail;
    }

    /**
     * @param PasswordResetRequestForm $form
     */
    public function request(PasswordResetRequestForm $form): void
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $form->email,
        ]);

        if(!$user) { throw new \RuntimeException('Users not found.'); }

        $user->requestPasswordReset();

        if(!$user->save()) { throw new \RuntimeException('Saving error.'); }

        $sent = \Yii::$app->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user])
            ->setFrom($this->supportEmail)
            ->setTo($user->email)
            ->setSubject('Password reset for ' . \Yii::$app->name)
            ->send();

        if(!$sent) { throw new \RuntimeException('sending error.'); }

    }

    /**
     * @param $token
     * @return void
     */
    public function validateToken($token): void
    {
        if(empty($token) || !is_string($token)) { throw new \RuntimeException('password reset token cant be blank.'); }
        if(!User::findByPasswordResetToken($token)){ throw new \RuntimeException('wrong password reset token'); }
    }

    /**
     * @param string $token
     * @param ResetPasswordForm $form
     *
     */
    public function reset(string $token, ResetPasswordForm $form): void
    {
        $user = User::findByPasswordResetToken($token);

        if(!$user) { throw new \RuntimeException('user not found'); }

        $user->resetPassword($form->password);

        if(!$user->save()) { throw new \RuntimeException('saving error.'); }

    }
}