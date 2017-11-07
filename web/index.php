<?php
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
//defined('YII_DEBUG') or define('YII_DEBUG', false);
//defined('YII_ENV') or define('YII_ENV', false);

//setlocale(LC_ALL, 'ru_RU.UTF8');
/* установка локалей только для строк чтобы не мучиться с представлениями FLOAT */
//setlocale(LC_COLLATE, 'ru_RU.utf-8', 'rus_RUS.utf-8', 'ru_RU.utf8', 'Russian_Russia.65001');
//setlocale(LC_CTYPE, 'ru_RU.utf-8', 'rus_RUS.utf-8', 'ru_RU.utf8', 'Russian_Russia.65001');

require(__DIR__ . '/../app/vendor/autoload.php');
require(__DIR__ . '/../app/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../app/config/web.php');

(new yii\web\Application($config))->run();
