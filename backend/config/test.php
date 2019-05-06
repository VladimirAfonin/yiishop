<?php
return [
    'id' => 'app-backend-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],

    'mailgun' => [
        'api' => 'key-579977450cd6ab90dceca5ba2ebb179d',
        'domain' => 'https://api.mailgun.net/v3/margaritaskripkina.ru',
    ],

    'accounts' => [
       1 => ['email' => 'info@margaritaskripkina.ru', 'password' => 'Va989126', 'host' => '{imap.yandex.com:993/imap/ssl}', 'folder' => ['inbox' => '{imap.yandex.com:993/imap/ssl}INBOX']],
       10 => ['email' => 'svetlana@margaritaskripkina.ru', 'password' => 'Va989126', 'host' => '{imap.yandex.com:993/imap/ssl}', 'folder' => [ 'inbox' => '{imap.yandex.com:993/imap/ssl}INBOX']],
    ],

    'imap.sinc.interval' => 600,

    'email_folders' => [
        'yandex' => [0 =>'sent', 1 => 'unknown', 2 => 'spam',3 =>  'deleted', 4 => 'draft', 5 => 'inbox'],
    ],

    'level_one' => [
        'level_two' => [
            'level_three' => 3
        ]
    ]
];
