<?php

return [
    'class' => 'yii\web\UrlManager',
    'hostInfo' => $params['frontendHostInfo'],
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '' => 'site/index',
        '<action:login|logout>' => 'auth/auth/<action>',
        '<action:signup>' => 'auth/signup/<action>',
        'contact' => 'contact/index',
        'about' => 'site/about',
        'cabinet' => '/cabinet/default/index'


    ]
];