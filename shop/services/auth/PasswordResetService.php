<?php

namespace shop\services\auth;

use shop\forms\auth\PasswordResetRequestForm;
use shop\entities\User;
use shop\forms\auth\ResetPasswordForm;
use yii\mail\MailerInterface;
use shop\collections\UserCollection;

class PasswordResetService
{
    private $supportEmail;
    private $mailer;
    private $usersCollect;

    public function __construct($supportEmail, MailerInterface $mailer, UserCollection $usersCollect)
    {
        $this->supportEmail = $supportEmail;
        $this->mailer = $mailer;
        $this->usersCollect = $usersCollect;
    }

    /**
     * @param PasswordResetRequestForm $form
     */
    public function request(PasswordResetRequestForm $form): void
    {
        $user = $this->usersCollect->getByEmail($form->email);
        if(!$user->isActive()) { throw new \RuntimeException('user is not active'); }

        $user->requestPasswordReset();
        $this->usersCollect->save($user);

        $sent = $this->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
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
        if(!$this->usersCollect->existsByPasswordResetToken($token)) { throw new \RuntimeException('wrong password reset token.'); }
    }

    /**
     * @param string $token
     * @param ResetPasswordForm $form
     *
     */
    public function reset(string $token, ResetPasswordForm $form): void
    {
        $user = $this->usersCollect->getByPasswordResetToken($token);
        $user->resetPassword($form->password);
        $this->usersCollect->save($user);
    }
}