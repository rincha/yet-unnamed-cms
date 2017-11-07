<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model app\models\UserAuthentication */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('app/user', 'Send account activation code');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-activate">

    <h1><?= Html::encode($this->title) ?></h1>

     <div class="user-activate-form">
        <?php $form = ActiveForm::begin();?>
        
        <div class="row">
        <div class="col-xs-4">
            <?php
            $types=[];
            foreach (Yii::$app->user->authentications['types'] as $k => $type) {
                $types[$k]=Yii::t('app/user', $type['name']);
            }        
            echo $form->field($model, 'type')->dropDownList($types);
            ?>
        </div>
        <div class="col-xs-8">
            <?=
            $form->field($model, 'uid')->textInput();
            ?>
        </div>
        </div>
         
        <?= $form->field($model, 'verify_code', ['enableClientValidation' => false])->widget(Captcha::className(), ['captchaAction' => '/site/captcha']) ?>
         
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    
</div>
