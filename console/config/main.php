<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
        'faker_fixture' => [
            'class'           => 'yii\faker\FixtureController',
            'namespace'       => 'common\fixtures',
            'fixtureDataPath' => '@common/fixtures',
            'templatePath'    => '@common/fixtures/templates',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'user' => [ // add config to console to rbac
            'class' => 'yii\web\User',
            'identityClass' => 'shop\entities\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'authManager' => [ // for rbac add config ...
            'class' => 'yii\rbac\PhpManager', // as default
//            'class' => 'rbac/AuthManager', // our 'extended' class from PhpManager
//            'defaultRoles' => ['guest'],
            'defaultRoles' => ['admin'],
            'itemFile' => '@app/rbac/items.php',
//            'itemFile' => '@app'.DIRECTORY_SEPARATOR . 'rbac' . DIRECTORY_SEPARATOR . 'items.php',
            'assignmentFile' => '@app/rbac/assignments.php',
            'ruleFile' => '@app/rbac/rules.php'
        ],

        'frontendUrlManager' =>  require __DIR__ . '/../../frontend/config/urlManager.php',
        'backendUrlManager' =>  require __DIR__ .  '/../../backend/config/urlManager.php',

    ],
    'params' => $params,
];
