<?php
return [
    'default'=>[
        'name'=>Yii::t('info', 'Information materials'),
        'actions'=>[
            'index'=>[
                'name'=>Yii::t('info', 'Information materials list'),
                'select'=>true,
                'columns'=>['title', 'name'],
                'query'=>function ($term) {
                    return \app\modules\info\models\Type::find()->andFilterWhere(['like','name',$term]);
                }
            ],
            'view'=>[
                'name'=>Yii::t('info', 'Select Information material'),
                'columns'=>['name','created_at'],
                'query'=>function ($term) {
                    return \app\modules\info\models\Info::find()->andFilterWhere(['like','name',$term]);
                }
            ],
        ]
    ],
];