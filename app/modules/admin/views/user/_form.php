<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); 
	echo Html::errorSummary($model);
	?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'new_password')->hint(Html::a('Изменить пароль','#',['id'=>'tmp_password_reset']))->passwordInput(['maxlength' => 64, 'disabled'=>true]) ?>

    <?= $form->field($model, 'status')->dropDownList(User::getStatusesList()) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/user', 'Create') : Yii::t('app/user', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs(
    "$('#tmp_password_reset').click(function(){
	if ($('#user-new_password').is(':disabled')) {
		$('#user-new_password').prop('disabled',false);
		$(this).text('Отмена');
	}
	else {
		$('#user-new_password').prop('disabled',true);
		$(this).text('Изменить пароль');
	}
	return false;
});"
);
?>