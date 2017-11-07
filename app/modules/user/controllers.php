<?php
return [
    'default'=>[
        'name'=>Yii::t('app/user', 'Account'),
        'actions'=>[
            'index'=>Yii::t('app/user', 'Account'),
            'authentications'=>Yii::t('app/user', 'Authentication accounts'),
            'authentication-create'=>Yii::t('app/user', 'Authentication accounts').': '.Yii::t('app/user', 'Add account'),
            'sessions'=>Yii::t('app/user', 'Sessions'),
            'security'=>Yii::t('app/user', 'Security'),
        ]
    ],
    'profile'=>[
        'name'=>Yii::t('app/user', 'Profile'),
        'actions'=>[
            'index'=>Yii::t('app/user', 'Profiles'),
        ],
    ],
];