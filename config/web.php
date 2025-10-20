<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'infotech-book-catalog-tz',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'LeQjV70ItYrRZ75eV-coEP6rEJI2xXw0',
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
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'itemFile' => '@app/rbac/items.php',
            'assignmentFile' => '@app/rbac/assignments.php',
            'ruleFile' => '@app/rbac/rules.php',
        ],
        // Queue
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@runtime/queue',
            'as log' => \yii\queue\LogBehavior::class,
        ],
        // URL Manager
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'books' => 'book/index',
                'books/create' => 'book/create',
                'books/<id:\d+>' => 'book/view',
                'books/<id:\d+>/edit' => 'book/update',
                'books/<id:\d+>/delete' => 'book/delete',
                
                'authors' => 'author/index',
                'authors/<id:\d+>' => 'author/view',
                'authors/<id:\d+>/subscribe' => 'author/subscribe',
                
                'reports/top-authors' => 'report/top-authors',
                'reports/top-authors/<year:\d{4}>' => 'report/top-authors',
            ],
        ],
    ],
    'params' => $params,
    // Контейнер зависимостей
    'container' => [
        'definitions' => [
            'app\services\BookService' => [],
            'app\services\SmsService' => [
                'apiKey' => 'emulator_key',
            ],
            'app\services\SubscriptionService' => [],
            'app\services\ReportService' => [],
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
