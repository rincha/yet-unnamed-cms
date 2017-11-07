<?php
$config = [
    'id' => 'yucms',
    'language' => '{LOCALE_LANGUAGE}',
    'sourceLanguage' => 'en-US',
    'timeZone'=>'{LOCALE_TIMEZONE}',

    'version' => '1.0.0.0',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => require(__DIR__ . '/components.php'),
    'modules' => require(__DIR__ . '/modules.php'),
    'params' => require(__DIR__ . '/params.php'),
    'layout'=>'@app/views/layouts/columns',
    //'defaultRoute'=>'info/default/view',
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1','192.168.1.*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1','192.168.1.*'],
    ];
}

return $config;
