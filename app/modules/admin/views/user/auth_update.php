<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserAuthentication */

$this->title = Yii::t('app', 'Update {modelClass}:', [
    'modelClass' =>  Yii::t('app/user','Authentication account')
]) . ' ' . $model->uid;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user->uid, 'url' => ['view', 'id' => $model->user->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-auth-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('auth_form', [
        'model' => $model,
    ]) ?>

</div>
