<?php
namespace shop\dispatchers\listeners;

use shop\dispatchers\events\UserSignUpRequested;
use shop\dispatchers\jobs\UserSignUpRequestNotify;
use yii\mail\MailerInterface;
use yii\queue\redis\Queue;

class UserSignUpRequestListener
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param UserSignUpRequested $event
     */
    public function handle(UserSignUpRequested $event)
    {
        $this->queue->push(new UserSignUpRequestNotify($event->user));
    }
}