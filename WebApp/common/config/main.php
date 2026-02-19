<?php
return [
    'name' => 'SciEventLink',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            // 'defaultRoles' => ['guest'],
        ],

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'pt-PT',
            'currencyCode' => 'EUR',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
        ],
    ],
];
