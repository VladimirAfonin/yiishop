<?php

namespace shop\dispatchers\events;

use shop\entities\User;

class UserSignUpRequested
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}