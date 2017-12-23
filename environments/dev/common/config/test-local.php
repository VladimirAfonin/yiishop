<?php
return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=localhost;port=3306;dbname=yiielis;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock',
            ]
        ],
    ]
);
