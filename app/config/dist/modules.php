<?php

return [
    'rbac' => [
        'class' => 'app\modules\rbac\RbacManager',
        'rulesPath' => [],
    ],
    'u' => [
        'class' => 'app\modules\user\Module',
    ],
    'admin' => [
        'class' => 'app\modules\admin\Admin',
        'additionalControllers'=>[
            'app\modules\banner\controllers\AdminController',
            'app\modules\forms\controllers\AdminController',
            'app\modules\info\controllers\AdminController',
            'app\modules\news\controllers\AdminController',
            'app\modules\post\controllers\AdminController',
            'app\modules\menu\controllers\AdminController',
            'app\modules\promo\controllers\AdminController',
        ]
    ],
    'files' => [
        'class' => 'app\modules\files\Module',
    ],
    'banner'=>[
        'class' => 'app\modules\banner\Module',
    ],
    'forms'=>[
        'class' => 'app\modules\forms\Module',
    ],
    'info'=>[
        'class' => 'app\modules\info\Module',
    ],
    'menu' => [
        'class' => 'app\modules\menu\Module',
    ],
    'news'=>[
        'class' => 'app\modules\news\Module',
    ],
    'post'=>[
        'class' => 'app\modules\post\Module',
    ],
    'promo'=>[
        'class' => 'app\modules\promo\Module',
    ],
];
