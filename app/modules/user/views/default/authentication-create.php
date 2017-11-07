<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $authentication app\models\UserAuthentication */
/* @var $user app\models\User */
/* @var $form yii\widgets\ActiveForm */


$this->title = Yii::t('app/user', 'Add account');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Account'),'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Connected accounts'),'url'=>['authentications']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app', 'Add')];
?>

<h1><?= $this->title ?>: <?= Yii::t('app/user', $authentication->typeName) ?></h1>

<div class="user-default-authentication-create">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($authentication, 'uid')->label(Yii::t('app/user', $authentication->typeName))->textInput(['maxlength' => true]) ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add') , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
