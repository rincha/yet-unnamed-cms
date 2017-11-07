<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => '{DB_DSN}',
    'username' => '{DB_USERNAME}',
    'password' => '{DB_PASSWORD}',
    'charset' => 'utf8',
    'tablePrefix' => 'yucms_',
    //'enableSchemaCache'=>true,
    'on afterOpen' => function($event) {
        $timezone_offset=timezone_offset_get(new DateTimeZone(Yii::$app->timeZone), new DateTime());
        $timezone_gmt_h= floor($timezone_offset/3600);
        $timezone_gmt_m= floor($timezone_offset%60);
        $timezone_gmt_str=
                ($timezone_gmt_h>=0?'+':'').str_pad($timezone_gmt_h, 2, '0', STR_PAD_LEFT).
                ':'.
                str_pad(abs($timezone_gmt_m), 2, '0', STR_PAD_LEFT);
        $event->sender->createCommand("SET time_zone=:tz;")->bindValue(':tz',$timezone_gmt_str)->execute();
        $lc= str_replace('-', '_', Yii::$app->language);
        $event->sender->createCommand("SET lc_time_names=:lc;")->bindValue(':lc',$lc)->execute();
    },
];
