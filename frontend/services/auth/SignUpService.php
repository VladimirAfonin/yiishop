<?php

namespace frontend\services\auth;


use frontend\forms\SignupForm;
use common\entities\User;
use yii\mail\MailerInterface;

class SignUpService
{
    private $mailer;
    private $supportEmail;

    public function __construct($supportEmail, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->supportEmail = $supportEmail;
    }

    /**
     * sign up action user
     *
     * @param SignupForm $form
     * @return User
     */
    public function signup(SignupForm $form): void
    {
        $user =  User::create($form->username, $form->email, $form->password);

        if(!$user->save()) {
            throw new \RuntimeException('saving error.');
        }

        $sent = $this->mailer
            ->compose(
                ['html' => 'emailConfirmToken-html', 'text' => 'emailConfirmToken-text'],
                ['user' => $user]
            )
            ->setTo($form->email)
            ->setFrom($this->supportEmail)
            ->setSubject('signup confirm for ' . \Yii::$app->name)
            ->send();

        if(!$sent) { throw new \RuntimeException('email sending error.'); }
    }

    /**
     * confirm email activation
     *
     * @param $token
     * @return void
     */
    public function confirm($token): void
    {
        if(empty($token)) { throw new \RuntimeException('empty confirm token.'); }

        $user = User::findOne(['email_confirm_token' => $token]);

        if(!$user) { throw new \RuntimeException("user not found."); }

        $user->confirmSignup();

        if(!$user->save()) { throw new \RuntimeException("saving error."); }

    }
}