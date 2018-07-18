<?php
namespace common\rbac\rules;

use yii\base\InvalidCallException;
use yii\rbac\Rule;

class ProfileOwnerRule extends Rule
{
    public function execute($userId, $item, $params)
    {
        if (empty($params['user'])) {
            throw new InvalidCallException('specify user.');
        }
        return $params['user']->id == $userId;
    }

}