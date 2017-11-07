<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\UserRestore */
/* @var $code string */

$this->title = Yii::t('app/user', 'Restore access');
$this->params['breadcrumbs'][] = ['label'=>$this->title,'url'=>['restore']];
$this->params['breadcrumbs'][] = Yii::t('app/user', 'Reset token');
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['method'=>'get','action'=>['restore']]);?>
    
    <?= $form->errorSummary($model) ?>

    <div class="row">
        <div class="col-xs-4">
            <?php    
            echo $form->field($model, 'type')->textInput(['readOnly'=>true,'name'=>'type']);
            ?>
        </div>
        <div class="col-xs-8">
            <?=
            $form->field($model, 'uid')->textInput(['readOnly'=>true,'name'=>'uid']);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::label($model->getAttributeLabel('reset_token'), 'reset-token', ['class'=>'control-label']) ?>
        <?= Html::textInput('code', $code, ['id'=>'reset-token','class'=>'form-control']) ?>
    </div>
	
    <div class="form-group">        
        <?= Html::submitButton(Yii::t('app/user', 'Restore'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

