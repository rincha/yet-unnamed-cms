#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

$config = [
    'id' => 'yucms-install-console',
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'basePath' => __DIR__,
    'aliases'=>[
        '@app' => __DIR__,
        '@webroot' => realpath(__DIR__ . '/../'),
        '@web' => '/',
    ],
    'controllerNamespace' => 'app\commands',
    'components'=>[
        'db'=>[
            'class' => 'yii\db\Connection',
        ],
        'authManager'=>[
            'class' => 'app\modules\rbac\components\DbManager',
        ],
        'i18n' => require(__DIR__ . '/config/i18n.php'),
    ],
];

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);
