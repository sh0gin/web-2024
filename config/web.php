<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'lmgiyuoguio',
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser',

            ],

        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode == 404) {
                    $response->data = [
                        'code' => 404,
                        'message' => 'Не найдено'
                    ];
                }
                if ($response->statusCode == 401) {
                    $response->data = [
                        'message' => 'Unauthorized'
                    ];
                }
                if ($response->statusCode == 403) {
                    $response->data = [
                        'code' => 404,
                        'message' => 'Недоступно для вас'
                    ];
                }
            },
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],

                "POST api/user/login" => 'user/login',
                "OPTION api/user/login" => 'user/option',

                "POST api/meet" => 'meetings/create',
                "OPTION api/meet" => 'meetings/option',

                "POST api/meet/<meetHash>/login" => 'meetings/join-meeting',
                "OPTION api/meet/<meetHash>/login" => 'meetings/option',

                "DELETE api/meet/<meetHash>/<leaderHash>/user/<userId>" => 'meetings/delete-user',
                "OPTION api/meet/<meetHash>/<leaderHash>/user/<userId>" => 'meetings/option',

                "DELETE api/meet/<meetHash>/<leaderHash>/file/<filename>" => 'meetings/delete-file',
                "OPTION api/meet/<meetHash>/<leaderHash>/file/<filename>" => 'meetings/option',

                "PATCH api/meet/<meetHash>/<leaderHash>" => 'meetings/block-meetings',
                "OPTION api/meet/<meetHash>/<leaderHash>" => 'meetings/option',

                "POST api/meet/<meetHash>/<leaderHash>" => 'meetings/upload-files',
                "OPTION api/meet/<meetHash>/<leaderHash>" => 'meetings/option',

                "POST api/meet/<meetHash>/<leaderHash>/invite" => 'meetings/send-emails',
                "OPTION api/meet/<meetHash>/<leaderHash>/invite" => 'meetings/option',

                "GET api/meet/<meetHash>/" => 'meetings/view-meeting',
                "OPTION api/meet/<meetHash>/" => 'meetings/option',

                "PATCH api/meet/<meetHash>/user/<userID>" => 'meetings/change-availables',
                "OPTION api/meet/<meetHash>/user/<userID>" => 'meetings/option',

                "DELETE api/meet/<meetHash>/<leaderHash>" => 'meetings/delete-meeting',
                "OPTION api/meet/<meetHash>/user/<userID>" => 'meetings/option',

                "GET api/logout" => 'user/logout',
                "OPTION api/logout" => 'user/logout',

                "GET api/meet/<meetHash>/file/<filename>" => 'meetings/download-file',
                "OPTION api/logout" => 'meetings/download-file',

                "GET api/meet/<meetHash>/<leaderHash>" => 'meetings/check-leader',
                "OPTION api/logout" => 'meetings/logout',

                "GET api/profile" => 'user/profile',
                "OPTION api/logout" => 'user/profile',

                
            ],
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
