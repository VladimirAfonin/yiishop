<?php

namespace shop\forms\manage\User;

use yii\base\Model;
use shop\entities\User;

class UserEditForm extends Model
{
    public $username;
    public $email;

    public $userObj;

    public function __construct(User $user, array $config = [])
    {
        $this->username = $user->username;
        $this->email = $user->email;
        $this->userObj = $user;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['username', 'email'], 'required'],
            ['email', 'email'],
            [['email'], 'string', 'max' => 255],
            [['username', 'email'], 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->userObj->id]], // уникальность - не должно совпадать с текущим id
//            ['password', 'string', 'min' => 6],
        ];
    }
}