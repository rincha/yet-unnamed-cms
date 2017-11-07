<?php
return [
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'defaultTimeZone'=>'{LOCALE_TIMEZONE}',
        'dateFormat' => 'php:Y-m-d',
        'datetimeFormat' => 'php:Y-m-d H:i:s',
        'timeFormat' => 'php:H:i:s',
    ],
    'i18n' => require(__DIR__ . '/i18n.php'),
    'request' => [
        'enableCsrfValidation' => true,
        'enableCsrfCookie' => true,
        'enableCookieValidation' => true,
        'cookieValidationKey' => '{COOKIE_VALIDATION_KEY}',
    ],
    'urlManager' => [
        'cache' => false,
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'suffix' => '/',
        'normalizer'=>[
            'class' => 'yii\web\UrlNormalizer',
            'normalizeTrailingSlash' => true,
        ],
        'rules' => require(__DIR__ . '/urlrules.php'),
    ],
    'cache' => [
        'class' => 'yii\caching\DbCache',
    ],
    'user' => [
        'class' => 'app\common\components\User',
        //'on afterRenewAuthStatus'=>function($event){},
        'autoUsername'=>true, //uncomment for don`t use custom usernames
        'rememberCookieLifetime' => 60 * 60 * 24 * 30, //30 days
        'identityClass' => 'app\models\User',
        'enableSession' => true,
        'enableAutoLogin' => true,
        'loginUrl' => ['user/login'],
        'identityCookie' => [
            'name' => '_identity',
            'httpOnly' => true,
        ],
        'profiles' => [

        ],
        'profilesRequired' => [

        ],
        'authentications' => [
            'max_login_try' => 3,
            'enabled' => true,
            'required' => true,
            'types' => [
                'email' => [
                    'id'=>'email',
                    'name' => 'E-mail',
                    'enabled' => true,
                    'required' => true,
                    'activation' => true,
                    'iconClass' => 'fa fa-envelope',
                    'loginUidPatterns' => [
                        '/.+@.+/ui' => 'E-mail address',
                    ],
                ],
            ],
        ],
    ],
    'session' => [
        'class' => 'app\common\components\DbSession',
        'gCProbability' => 1,
        'timeout' => 60 * 60 * 24 * 7, //30 days
        'guestTimeout' => 60*15, //30 min
        'cookieParams' => [
            'httpOnly' => true,
            'lifetime' => 0,
        ],
    ],
    'authManager' => [
        'class' => 'app\modules\rbac\components\DbManager',
        //'cache'=>'cache',
        'defaultRoles' => ['Authenticated'],
        'defaultRolesConfig' => [
            'Authenticated'=>[
                'rule_name'=>'ItemDataEvalRule',
                'data'=>'return !\Yii::$app->user->isGuest;',
            ],
        ],
    ],
    'errorHandler' => [
        'errorAction' => 'site/error',
    ],
    'mailer' => [
        'useFileTransport' => true,
        'fileTransportPath' => '@app/runtime/tmp',
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning',],
                'except'=>['yii\web\HttpException:40*'],
            ],
        ],
    ],
    'db' => require(__DIR__ . '/db.php'),
    'view' => [
        'class' => 'app\common\web\View',
        'theme' => [
            'basePath' => '@app/themes/default',
            'baseUrl' => '@web/themes/default',
            'pathMap' => [
                '@app/views' => '@app/themes/default/views',
                '@app/modules' => '@app/themes/default/modules',
            ],
        ],
    ],
    'assetManager' => [
        'class' => 'yii\web\AssetManager',
        'forceCopy'=>YII_ENV_DEV,
        'bundles' => [
            'yii\web\JqueryAsset' => [
                'js' => [
                    YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'
                ]
            ],
            'yii\bootstrap\BootstrapAsset' => [
                'css' => [
                    YII_ENV_DEV ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                ]
            ],
            'yii\bootstrap\BootstrapPluginAsset' => [
                'js' => [
                    YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                ]
            ]
        ],
    ],
];
