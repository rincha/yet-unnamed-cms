<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app/user', 'Profiles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Account'), 'url' => ['/u/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Profiles')];
?>
<div class="view-user-profile-index">
    <h1><?= $this->title ?></h1>

    <?php
    $data = [];
    foreach (Yii::$app->user->profiles as $id=>$p) {
        $pClassName=$p['class'];
        $pModel=new $pClassName();
        $data[] = [
            'name' => $id,
            'active' => $model->{$p['property']}?1:0,
            'url' => $pModel->getProfileUrl(),
            'title' => $pModel->getProfileLabel(),
        ];
    }
    ?>

    <?=
    GridView::widget([
        'dataProvider' => new ArrayDataProvider(['allModels' => $data]),
        'showHeader'=>false,
        'rowOptions'=>function($model, $key, $index, $grid){
            if ($model['active'])
                return ['class'=>'success'];
            else
                return [];
        },
        'columns' => [
            'title',
            [
                'label' => '',
                'value' => function($data) {
                    return Html::a(
                            $data['active']?'<i class="glyphicon glyphicon-eye-open"></i> '.Yii::t('yii', 'View'):'<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add'),
                            $data['url'],
                            ['class'=>'btn btn-xs btn-default']
                    );
                },
                'format' => 'html',
                'contentOptions'=>['class'=>'text-right']
            ]
        ],
    ])
    ?>

</div>
