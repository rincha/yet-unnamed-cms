<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\UserAuthentication;

/* @var $this yii\web\View */
/* @var $model app\models\UserAuthentication */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-auth-form">

    <?php $form = ActiveForm::begin();
	echo Html::errorSummary($model);
	?>

    <?= $form->field($model, 'type')->dropDownList(UserAuthentication::getTypesList()) ?>

    <?= $form->field($model, 'uid')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'status')->dropDownList(UserAuthentication::getStatusesList()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>