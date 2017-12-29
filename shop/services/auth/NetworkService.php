<?php

namespace shop\services\auth;

use shop\collections\UserCollection;
use shop\forms\auth\LoginForm;
use shop\entities\User;

class NetworkService
{
    private $_userCollection;

    public function __construct(UserCollection $userCollection)
    {
        $this->_userCollection = $userCollection;
    }

    public function auth($network, $identity): User
    {
        if($user = $this->_userCollection->findByNetworkIdentity($network, $identity)) {
            return $user;
        }

        $user = User::signupByNetwork($network, $identity);
        $this->_userCollection->save($user);

        return $user;
    }
}