<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Nav;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rbac', 'Rights');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    echo Nav::widget([
        'items' => [
            ['label' => Yii::t('rbac', 'Rights')],
            ['label' => Yii::t('rbac', 'Auth Items'), 'url' => ['auth-item-index']],
            ['label' => Yii::t('rbac', 'Rules'), 'url' => ['auth-rule-index']],
        ],
        'options' => ['class' => 'nav-pills'],
    ]);
    ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            'id',
            [
                'attribute' => 'username',
                'value' => 'uid',
            ],
            [
                'attribute' => '_authentication',
                //'label' => 'Аккаунты',
                'value' => function($model) {
                    $result = [];
                    foreach ($model->authentications as $a) {
                        $result[] = $a->type . ':' . $a->uid;
                    }
                    return implode("\n", $result);
                },
                'format' => 'ntext',
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->statusName;
                },
                'filter' => Html::activeDropDownList($model, 'status', array_merge(['' => ''], User::getStatusesList()), ['class' => 'form-control']),
            ],
            [
                'label' => Yii::t('rbac', 'Auth Items'),
                'attribute' => 'assignment_item_name',
                'value' => function($model) {
                    $list = [];
                    foreach ($model->authAssignments as $a) {
                        $list[] = $a->item_name;
                    }
                    return implode(', ', $list);
                },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'urlCreator' => function($action, $model, $key, $index) {
                            switch ($action) {
                                case 'view': return \yii\helpers\Url::to(['user-auth-view', 'id' => $model->id]);
                                    break;
                                default: return \yii\helpers\Url::to('#');
                                    break;
                            }
                        },
                            ],
                        ],
                    ]);
    ?>

</div>
