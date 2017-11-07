<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserAuthentication */
/* @var $user app\models\User */

$this->title = Yii::t('app', 'Create {modelClass}:', [
    'modelClass' =>  Yii::t('app/user','Authentication account')
]) . ' ' . $user->uid;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->uid, 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="user-auth-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('auth_form', [
        'model' => $model,
    ]) ?>

</div>
