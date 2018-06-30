<?php
namespace rbac;

use yii\rbac\Item;
use yii\rbac\Rule;
use Yii;

class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    /**
     * Executes the rule.
     *
     * @param string|int $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (!Yii::$app->user->isGuest) {
            $group = Yii::$app->user->identity->role; // field 'role' in DB ...
            if ($item->name === 'admin') {
                return $group == 1;
            } elseif ($item->name == 'author') {
                return $group == 1 || $group == 2;
            }
        }
        return false;
    }
}