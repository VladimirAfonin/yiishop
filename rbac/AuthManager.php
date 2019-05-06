<?php
namespace rbac;

use shop\entities\User;
use Yii;
use yii\rbac\Assignment;
use yii\rbac\PhpManager;

/**
 * Class AuthManager - our class hybrid between PhpManager & DbManager
 * @package rbac
 */
class AuthManager extends PhpManager
{
    public function getAssignments($userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            $assignment = new Assignment();
            $assignment->userId = $userId;
            $assignment->roleName = $user->role;
            return [$assignment->roleName => $assignment];
        }
        return [];
    }

    public function getAssignment($roleName, $userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            if ($user->role == $roleName) {
                $assignment = new Assignment();
                $assignment->userId = $userId;
                $assignment->roleName = $user->role;
                return $assignment;
            }
        }
        return null;
    }

    /**
     * @param \yii\rbac\Permission|\yii\rbac\Role $role
     * @param int|string $userId
     * @return void
     */
    public function assign($role, $userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            $this->setRole($user, $role);
        }
    }

    /**
     * @param \yii\rbac\Permission|\yii\rbac\Role $role
     * @param int|string $userId
     * @return bool|void
     */
    public function revoke($role, $userId): void
    {
        if ($userId && $user = $this->getUser($userId)) {
            if ($user->role == $role->name) {
                $this->setRole($user, null);
            }
        }
    }

    public function revokeAll($userId)
    {
        if ($userId && $user = $this->getUser($userId)) {
            $this->setRole($user, null);
        }
    }

    /**
     * @param $userId
     * @return \yii\web\IdentityInterface
     */
    private function getUser($userId)
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->id == $userId) {
            return Yii::$app->user->identity;
        } else {
            return User::findOne($userId);
        }
    }

    /**
     * @param $user
     * @param $roleName
     */
    private function setRole($user, $roleName)
    {
        /** @var User $user */
        $user->role = $roleName;
        $user->updateAttributes(['role' => $roleName]);
    }
}