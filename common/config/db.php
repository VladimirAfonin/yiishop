<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;port=3306;dbname=yiielis;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock',
//    'dsn' => 'mysql:host=127.0.0.1;dbname=yiigeek',
    'username' => 'admin',
    'password' => 'admin',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
