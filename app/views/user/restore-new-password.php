<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\UserRestore */
/* @var $user app\models\User */
/* @var $code string */

$this->title = Yii::t('app/user', 'Reset password');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Restore access'), 'url' => ['restore']];
$this->params['breadcrumbs'][] = Yii::t('app/user', 'Reset password');
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(['method' => 'post', 'action' => ['restore', 'type' => $model->type, 'uid' => $model->uid, 'code' => $code]]); ?>
    <?= $form->errorSummary($user) ?>
    <?= $form->field($user, 'new_password')->passwordInput(['data-toggle'=>'password'])->label(Yii::t('app/user', 'New password')) ?>
    <?= $form->field($user, 'new_password_verify')->passwordInput(['data-toggle'=>'password']) ?>
    <div class="form-group">        
    <?= Html::submitButton(Yii::t('app/user', 'Change password & Log in'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>