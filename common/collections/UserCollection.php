<?php

namespace common\collections;

use common\entities\User;

class UserCollection
{


    /**
     * @param string $token
     * @return User
     */
    public function getByEmailConfirmToken(string $token): User
    {
        return $this->getBy(['email_confirm_token' => $token]);
    }

    /**
     * @param string $email
     * @return User
     */
    public function getByEmail(string $email): User
    {
        return $this->getBy(['email' => $email]);
    }

    /**
     * @param string $token
     * @return User
     */
    public function existsByPasswordResetToken(string $token): User
    {
        return (bool) User::findByPasswordResetToken($token);
    }

    /**
     * @param string $token
     * @return User
     */
    public function getByPasswordResetToken(string $token): User
    {
        if(!$user = User::findByPasswordResetToken($token)) { throw new NotFoundException('user is not found.'); }
        return $user;
    }

    /**
     * @param User $user
     */
    public function save(User $user): void
    {
        if(!$user->save()) { throw new NotFoundException('saving error.'); }
    }

    /**
     * @param array $condition
     * @return User
     */
    private function getBy(array $condition): User
    {
        if(!$user = User::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('user not found');
        }
        return $user;
    }
}