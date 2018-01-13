<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
//        '@staticRoot' => '@common/static/web',
//        '@static' => '@common/static',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    // our container class
    'bootstrap' => [
        'common\bootstrap\SetUp'
    ],

    'components' => [
        'db' => require(dirname(__DIR__)) . '/config/db.php',
        'cache' => [
            'class' => 'yii\caching\FileCache',
//            'class' => 'yii\caching\MemCache',
//            'useMemcached' => true,
            // one dir for
            'cachePath' => '@common/runtime/cache',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => 'google_client_id',
                    'clientSecret' => 'google_client_secret',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => 'facebook_client_id',
                    'clientSecret' => 'секретный_ключ_facebook_client',
                ],
            ],
        ]
    ],
];
