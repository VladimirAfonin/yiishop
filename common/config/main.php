<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    // our container class
    'bootstrap' => [
        'common\bootstrap\SetUp'
    ],
    'components' => [
        'db' => require(dirname(__DIR__)) . '/config/db.php',
        'cache' => [
//            'class' => 'yii\caching\FileCache',
            'class' => 'yii\caching\MemCache',
            'useMemcached' => true,
            // one dir for
//            'cachePath' => '@common/runtime/cache',
        ],
    ],
];
