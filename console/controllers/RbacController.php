<?php
namespace console\controllers;

use shop\entities\User;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use console\rbac\AuthorRule;

class RbacController extends Controller
{
    /**
     * generate roles
     */
    public function actionInit()
    {
        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();

        $rule = new AuthorRule();
        $auth->add($rule);

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);

        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'update post';
        $auth->add($updatePost);

        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $rule->name;
        $auth->add($updateOwnPost);

        $auth->addChild($updateOwnPost, $updatePost);

        $user = $auth->createRole('user');
        $auth->add($user);

        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $auth->addChild($admin, $user);
        $auth->addChild($admin, $createPost);
        $this->stdout('Done!' . PHP_EOL,Console::FG_BLUE);
    }

    /**
     * test actions
     */
    public function actionTest()
    {
        Yii::$app->set('request', new \yii\web\Request());
        $auth = Yii::$app->getAuthManager();

        $user = new User(['id' => 1,'username' => 'User']);
        $auth->revokeAll($user->id);

//        echo 'Roles for user:' . PHP_EOL;
        print_r($auth->getRolesByUser($user->id));
        $auth->assign($auth->getRole('user'), $user->id);
        echo PHP_EOL;

        // check if we have auth user
        Yii::$app->user->can('admin');
        Yii::$app->authManager->checkAccess($user->id, 'admin');

        // =======
        $this->stdout("check access for {$user->username}\n\n", Console::FG_BLUE);
        Yii::$app->user->login($user);
        $post = new Post(['title' => 'Example post', 'user_id' => $user->id]);
        $this->show('create post', Yii::$app->user->can('createPost'));
        $this->show('update own post', Yii::$app->user->can('updateOwnPost',['post' => $post]));

    }

    public function show($message, $value)
    {
        $result = $value ? 'true' : 'false';
        $this->stdout("$message: $result\n\n", Console::FG_RED);
    }
}