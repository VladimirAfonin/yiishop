<?php

namespace shop\services\manage;


use shop\collections\UserCollection;
use shop\forms\manage\User\UserCreateForm;
use shop\entities\User;
use shop\forms\manage\User\UserEditForm;

class UserManageService
{
    private $_userCollection;

    public function __construct(UserCollection $userCollection)
    {
        $this->_userCollection = $userCollection;
    }

    public function create(UserCreateForm $form): User
    {
        $user = User::createFromAdmin(
            $form->username,
            $form->email,
            $form->password
        );

        $this->_userCollection->save($user);
        return $user;
    }

    public function edit($id, UserEditForm $form)
    {
        $user = $this->_userCollection->get($id);
        $user->editFromAdmin($form->username, $form->email);
        $this->_userCollection->save($user);
    }
}