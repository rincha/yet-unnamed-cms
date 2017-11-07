<?php

use yii\helpers\Html;
use app\common\grid\GridView;
use app\models\User;
use app\modules\post\Module;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/user', 'Users');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('post','Posts'), 'url'=>['/post/admin/index']];
$this->params['breadcrumbs'][] = Yii::t('app/user', 'Users');
?>
<div class="post-admin-users">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            'created_at:datetime',
            [
                'attribute' => '_auth_assigment_role',
                'value' => function(User $model){
                    $btns=[];
                    foreach ([Module::ROLE_AUTHOR,Module::ROLE_ADMIN] as $role) {
                        if (isset($model->authItems[$role])) {
                            $btns[]= Html::a(
                                    Yii::t('post', 'revoke {role}',['role'=>Yii::t('post', substr($role, 4))]),
                                    ['users-revoke','id'=>$model->id,'role'=>$role],
                                    [
                                        'class'=>'btn btn-xs btn-danger',
                                        'data-method'=>'post',
                                        'data-confirm'=>Yii::t('post', 'Are you sure to revoke {role} role?',['role'=>Yii::t('post', substr($role, 4))])
                                    ]
                            );
                        }
                        else {
                            $btns[]= Html::a(
                                    Yii::t('post', 'assign {role}',['role'=>Yii::t('post', substr($role, 4))]),
                                    ['users-assign','id'=>$model->id,'role'=>$role],
                                    [
                                        'class'=>'btn btn-xs btn-success',
                                        'data-method'=>'post',
                                        'data-confirm'=>Yii::t('post', 'Are you sure to assign {role} role?',['role'=>Yii::t('post', substr($role, 4))])
                                    ]
                            );
                        }
                    }
                    return implode(' ', $btns);
                },
                'filter'=>[
                    Module::ROLE_AUTHOR=>Yii::t('post', 'Author'),
                    Module::ROLE_ADMIN=>Yii::t('post', 'Admin'),
                ],
                'format'=>'raw',
            ],
        ],
    ]);
    ?>

</div>
