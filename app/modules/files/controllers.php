<?php
return [
    'default'=>[
        'name'=>Yii::t('files', 'Files'),
        'actions'=>[
            'view'=>[
                'name'=>\app\modules\files\models\Folder::getTypeList()[\app\modules\files\models\Folder::TYPE_GALLERY],
                'select'=>false,
                'columns'=>['name'],
                'query'=>function ($term) {
                    return \app\modules\files\models\Folder::find()->where(['type'=>\app\modules\files\models\Folder::TYPE_GALLERY])->andFilterWhere(['like','name',$term]);
                }
            ],
        ]
    ],
];