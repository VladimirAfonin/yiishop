<?php

namespace shop\dispatchers\events;

use shop\entities\User;

class UserSignUpConfirmed
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}