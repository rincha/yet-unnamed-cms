<?php
return [
    'default'=>[
        'name'=>Yii::t('forms', 'Forms'),
        'actions'=>[
            'view'=>[
                'name'=>Yii::t('forms', 'Select form'),
                'columns'=>['name','created_at'],
                'query'=>function ($term) {
                    return \app\modules\forms\models\Form::find()->andFilterWhere(['like','name',$term]);
                }
            ],
        ]
    ],
];