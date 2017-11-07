<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = Yii::t('app/user', 'Users').' :: '.$model->uid;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->uid;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app/user', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'statusName',
            'auth_key',
            'created_at',
            'updated_at',
        ],
    ])
    ?>


    <h2>Аккаунты</h2>

    <p>
        <?= Html::a(Yii::t('app', 'Add'), ['auth-create', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    foreach ($model->authentications as $a) {
        ?>
        <h3><?= $a->type ?></h3>
        <?=
        Html::a(Yii::t('app', 'Delete'), ['account-delete', 'id' => $a->id], [
            'class' => 'btn btn-danger btn-xs',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
        <?= Html::a(Yii::t('app', 'Update'), ['auth-update', 'id' => $a->id], ['class' => 'btn btn-primary btn-xs']) ?>
        <?=
        DetailView::widget([
            'model' => $a,
            'attributes' => [
                'uid',
                'verification',
                'verification_expire',
                'statusName',
                'created_at',
                'updated_at',
            ],
        ])
        ?>
    <?php
}
?>
</div>
