<?php
namespace shop\dispatchers\jobs;

use shop\dispatchers\events\UserSignUpRequested;
use yii\mail\MailerInterface;
use yii\queue\JobInterface;
use yii\queue\Queue;

class UserSignUpRequestNotify implements JobInterface
{
    private $user;

    public function __construct($event)
    {
        $this->user = $event->user;
    }

    public function execute($queue)
    {
        $sent = $this->getMailer()
            ->compose(
                ['html' => 'emailConfirmToken-html', 'text' => 'emailConfirmToken-text'],
                ['user' => $this->user]
            )
            ->setTo($this->user->email)
//            ->setFrom($this->supportEmail)
            ->setSubject('signup confirm for ' . \Yii::$app->name)
            ->send();

        if(!$sent) { throw new \RuntimeException('email sending error.'); }
    }

    private function getMailer(): MailerInterface
    {
        return \Yii::$container->get(MailerInterface::class);
    }

    /*private function getUsers(): UserRepo
    {
        return \Yii::container->get(UserRepository::class);
    }*/

}