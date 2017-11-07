<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use app\models\UserAuthentication;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $authentication app\models\UserAuthentication */
/* @var $profiles yii\db\ActiveRecord[] of profile models*/

$this->title = Yii::t('app/user', 'Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-register">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-register-form">

        <?php
        $form = ActiveForm::begin(['action' => ['register'], 'enableClientValidation' => false]);
        if ($model->errors) {
            ?><div class="alert alert-danger"><?= Html::errorSummary($model); ?></div><?php
        }
        ?>

        <?php if (!Yii::$app->user->autoUsername) {echo $form->field($model, 'username')->textInput(['maxlength' => 64]);} ?>

        <?php
        $uap=  Yii::$app->user->authentications;


        if (Yii::$app->user->authentications && Yii::$app->user->authentications['enabled'] && Yii::$app->user->authentications['required']) {
            $uap=Yii::$app->user->authentications;
            $uap['types']=[];
            foreach (Yii::$app->user->authentications['types'] as $k => $type) {
                if ($type['activation']) {
                    $uap['types'][$k]=$type;
                }
            }
        ?>
            <p><?= Yii::t('app/user', count($uap['types'])>1?'Select activation method:':'') ?></p>
            <?php
            $items=[];
            $first=true;
            foreach ($uap['types'] as $k => $type) {
                $a=new UserAuthentication();
                $a->scenario='register';
                $a->type=$k;
                if ($a->type==$authentication->type) {
                    $a->attributes=$authentication->attributes;
                    $a->addErrors($authentication->errors);
                }
                $items[]=[
                    'label' => Yii::t('app/user', $a->typeParams['name']),
                    'content' => $this->render('_activate_method',['model'=>$a,'user'=>$model,'form'=>$form, 'disabled'=>count($uap['types'])>1]),
                    'contentOptions' => $a->type==$authentication->type?['class' => 'in']:['class' => 'out'],
                    'options'=>['id'=>'tmp_reg_auth_'.$a->type]
                ];
                $first=false;
            }
            if (count($uap['types'])>1) {
                echo \yii\bootstrap\Collapse::widget(['items'=>$items,'clientEvents'=>[
                    'shown.bs.collapse'=>'function(){'
                    . '$(this).find(".panel-body:visible input").prop("disabled",false);'
                    . '}',
                    'hidden.bs.collapse'=>'function(){'
                    . '$(this).find(".panel-body:hidden input").prop("disabled",true);'
                    . '}',
                ]]);
            }
            else {
                echo Html::tag(
                        'div',
                        Html::tag(
                                'div',
                                Yii::t('app/user', $a->typeParams['name']),
                                ['class'=>'panel-heading']
                        ).Html::tag(
                                'div',
                                $items[0]['content'],
                                ['class'=>'panel-body']
                        ),
                        ['class'=>'panel panel-default']
                );
            }
            ?>
        <?php
        }
        ?>

        <?php
    foreach (Yii::$app->user->profilesRequired as $id=>$options) {
        if (isset($profiles[$id])) {
            $m=$profiles[$id];
        }
        else {
            $m=new Yii::$app->user->profiles[$id]['class'];
        }
        echo $this->render($options['fields'],['model'=>$m, 'form'=>$form]);
    }
        ?>

        <?= $form->field($model, 'new_password')->passwordInput(['maxlength' => 64, 'data-toggle'=>'password']) ?>

        <?= $form->field($model, 'new_password_verify')->passwordInput(['maxlength' => 64, 'data-toggle'=>'password']) ?>

        <?= $form->field($model, 'verify_code', ['enableClientValidation' => false])->widget(Captcha::className(), ['captchaAction' => '/site/captcha']) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app/user', 'Register'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>



</div>
