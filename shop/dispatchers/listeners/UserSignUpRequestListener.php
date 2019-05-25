<?php
namespace shop\dispatchers\listeners;

use shop\dispatchers\events\UserSignUpRequested;
use yii\mail\MailerInterface;

class UserSignUpRequestListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserSignUpRequested $event)
    {
        $sent = $this->mailer
            ->compose(
                ['html' => 'emailConfirmToken-html', 'text' => 'emailConfirmToken-text'],
                ['user' => $event->user]
            )
            ->setTo($event->user->email)
//            ->setFrom($this->supportEmail)
            ->setSubject('signup confirm for ' . \Yii::$app->name)
            ->send();

        if(!$sent) { throw new \RuntimeException('email sending error.'); }
    }
}