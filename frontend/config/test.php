<?php
return [
    'id' => 'app-frontend-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
            'appendTimestamp' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];
