<?php
return [
        'translations' => [
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'app' => 'app.php',
                    'app/user' => 'user.php',
                    'app/settings' => 'settings.php',
                    'app/widgets' => 'widgets.php',
                    'app/mail' => 'mail.php',
                ],
            ],
            'user*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/user/messages',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'user/common' => 'common.php',
                ],
            ],
            'rbac*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/rbac/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'rbac' => 'rbac.php',
                ],
            ],
            'files*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/files/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'files' => 'files.php',
                ],
            ],
            'banner' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/banner/messages/',
                'sourceLanguage' => 'en-US',
            ],
            'forms*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/forms/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'forms' => 'forms.php',
                ],
            ],
            'info*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/info/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'info' => 'info.php',
                ],
            ],
            'menu*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/menu/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'menu' => 'menu.php',
                ],
            ],
            'news*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/news/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'news' => 'news.php',
                ],
            ],

            
            'post*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/post/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'news' => 'post.php',
                ],
            ],
            'promo*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/modules/promo/messages/',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'news' => 'promo.php',
                ],
            ],
        ],
    ];