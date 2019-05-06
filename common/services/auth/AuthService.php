<?php

namespace common\services\auth;

use shop\collections\UserCollection;
use shop\forms\LoginForm;
use shop\entities\User;

class AuthService
{
    private $_userCollection;

    public function __construct(UserCollection $userCollection)
    {
        $this->_userCollection = $userCollection;
    }

    public function auth(LoginForm $form): User
    {
        $user = $this->_userCollection->getByUsername($form->username);
        if(!$user || !$user->isActive() || $user->validatePassword($form->password)) {
            throw new \RuntimeException('undefined user or password.');
        }
        return $user;
    }
}