<?php
return [
    [
        'pattern' => '',
        'route' => 'site/index',
        'defaults' => [],
    ],

    'a'=>'/admin/default/index',
    'a/<controller:[a-zA-Z-]+>/<action:[a-zA-Z-]+>'=>'/admin/<controller>/<action>',

    '<module:[a-zA-Z-]+>/<controller:[a-zA-Z-]+>/<action:[a-zA-Z-]+>' => '<module>/<controller>/<action>',
    '<controller:[a-zA-Z-]+>/<action:[a-zA-Z-]+>' => '<controller>/<action>',
    '<controller:[a-zA-Z-]+>' => '<controller>/index',
];

