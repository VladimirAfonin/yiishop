<?php
namespace shop\dispatchers\listeners;

use shop\dispatchers\events\UserSignUpConfirmed;

class UserSignUpConfirmListener
{
    private $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function handle(UserSignUpConfirmed $event)
    {
        $this->newsletter->subscribe($event->user->email);
    }
}