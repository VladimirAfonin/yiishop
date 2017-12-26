<?php

namespace frontend\services\auth;


use frontend\forms\SignupForm;
use common\entities\User;

class SignUpService
{
    public function signup(SignupForm $form): User
    {
        $user =  User::create($form->username, $form->email, $form->password);

        if(!$user->save()) {
            throw new \RuntimeException('saving error.');
        }

        return $user;
    }
}