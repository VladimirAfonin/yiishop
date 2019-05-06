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


    /**
     * @param $network
     * @param $identity
     * @return User
     */
    public function auth($network, $identity): User
    {
        if($user = $this->_userCollection->findByNetworkIdentity($network, $identity)) {
            return $user;
        }

        $user = User::signupByNetwork($network, $identity);
        $this->_userCollection->save($user);

        return $user;
    }

    /**
     * @param $id
     * @param $network
     * @param $identity
     * @return void|void
     */
    public function attach($id, $network, $identity): void
    {
        if($this->_userCollection->findByNetworkIdentity($network, $identity)) {
            throw new \RuntimeException('network is already signed up.');
        }
        $user = $this->_userCollection->get($id);
        $user->attachNetwork($network, $identity);
        $this->_userCollection->save($user);
    }
}