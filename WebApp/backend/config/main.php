<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'api' => [
            'class' => 'backend\modules\api\ModuleAPI',
        ],
    ],

    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => ['site/login', 'site/error', 'api/*'],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],

    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST api/login' => 'api/auth/login',
                'api/checkin/validate' => 'api/checkin/validate',

                'GET api/events' => 'api/event/index',
                'GET api/events/<id:\d+>' => 'api/event/view',
                'GET api/sessions/<id:\d+>' => 'api/session/view',

                'GET api/favorites' => 'api/favorite/index',
                'POST api/favorites' => 'api/favorite/create',
                'DELETE api/favorites/<id:\d+>' => 'api/favorite/delete',

                'POST api/feedback' => 'api/feedback/create',
                'GET api/sessions/<session_id:\d+>/questions' => 'api/question/index',
                'POST api/questions' => 'api/question/create',
            ],
        ],
    ],
    'params' => $params,
];