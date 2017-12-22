<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => require(dirname(__DIR__)) . '/config/db.php',
        'cache' => [
            'class' => 'yii\caching\FileCache',
            // one dir for
            'cachePath' => '@common/runtime/cache',
        ],
    ],
];
