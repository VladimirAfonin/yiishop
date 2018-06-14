<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php',
    require __DIR__ . '/test.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@staticRoot' => '@common/static/web', // $params['staticPath']
        '@static' => $params['staticPath'],// '@common/static',
    ],
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '/admin',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'user' => [
            'identityClass' => 'common\entities\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
            ],
            'on AfterLogin' => function (\yii\web\UserEvent $event) { // (\yii\base\Event $event)
                /** @var \shop\entities\User $user */
                $user = $event->identity; // $event->sender->identity
                $user->updateAttributes(['logget_at' => time()]);
            },// обновление времени логина юзера
        ],
        'myComponent' => [
            'class' => 'app\components\MyComponent',
            'name' => 'Petya', // public property: public $name; ... Yii::$app->myComponent->hello;
        ],
        /*
         * class MyComponent extends \yii\base\Component
         * {
         *      public $name = 'Vasya';
         *
         *      public function getHello()
         *      {
         *         return 'hello' . '$this->name . '!';
         *      }
         * }
         * */
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced',
            'cookieParams' => [
                'domain' => $params['cookieDomain'],
                'httpOnly' => true,
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'backendUrlManager' =>  require __DIR__ . '/urlManager.php',
        'frontendUrlManager' =>  require __DIR__ .  '/../../frontend/config/urlManager.php',
        //    'defaultRoute' => 'site/index',
        'urlManager' => function() {
            return Yii::$app->get('backendUrlManager');
        }

    ],


    // global config auth filter for all app.
    /*
    'as access' => [
        'class' => 'yii\filters\AccessControl',
            'except' => ['site/login', 'site/error'],
//            'only' => [],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    */

    'params' => $params,
];
