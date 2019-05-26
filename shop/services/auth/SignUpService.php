<?php

namespace shop\services\auth;


use shop\dispatchers\EventDispatcher;
use shop\dispatchers\events\UserSignUpConfirmed;
use shop\dispatchers\events\UserSignUpRequested;
use shop\forms\auth\SignupForm;
use shop\entities\User;
use shop\services\manage\TransactionManager;
use yii\mail\MailerInterface;

class SignUpService
{
    private $mailer;
    private $supportEmail;
    private $dispatcher;
    private $transaction;

    public function __construct(
        $supportEmail,
        MailerInterface $mailer,
        EventDispatcher $dispatcher,
        TransactionManager $transaction
    )
    {
        $this->mailer = $mailer;
        $this->supportEmail = $supportEmail;
        $this->dispatcher = $dispatcher;
        $this->transaction = $transaction;

    }

    /**
     * sign up action user
     *
     * @param SignupForm $form
     * @return void
     * @throws \Exception
     */
    public function signup(SignupForm $form)
    {
        $user =  User::create($form->username, $form->email, $form->password);


        $this->transaction->wrap(function() use($user){
           $user->save()
        });

        $this->dispatcher->dispatch(new UserSignUpRequested($user));

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
    public function confirm($token)
    {
        if(empty($token)) { throw new \RuntimeException('empty confirm token.'); }

        $user = User::findOne(['email_confirm_token' => $token]);

        if(!$user) { throw new \RuntimeException("user not found."); }

        $user->confirmSignup();

        if(!$user->save()) { throw new \RuntimeException("saving error."); }

        $this->dispatcher->dispatch($user->releaseEvents());

    }
}