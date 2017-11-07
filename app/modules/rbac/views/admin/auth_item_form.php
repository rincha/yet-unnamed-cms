<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\modules\rbac\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

	
    <?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-xs-6">
    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
		</div>
		<div class="col-xs-6">
    <?= $form->field($model, 'type')->dropDownList($model->typesLabels) ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-6">
    <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
		</div>
		<div class="col-xs-6">
	<?= $form->field($model, 'data')->textarea(['rows' => 2]) ?>
		</div>
	</div>
	
    <?= $form->field($model, 'rule_name')->textInput(['maxlength' => 64]) ?>
	
	<div class="row">
		<div class="col-xs-6">
        <?php
        $list=ArrayHelper::map(AuthItem::find()->all(), 'name', 'name');
        $childrens=ArrayHelper::map(Yii::$app->authManager->getPermissionsByRole($model->name), 'name', 'name');
        $res_list=array_diff($list, $childrens);
        ?>
	<?= $form->field($model, 'new_children')->dropDownList(
			$res_list,
			['multiple'=>true, 'size'=>20]
	) ?>
		</div>
		<div class="col-xs-6">
			<?= $this->render('auth_item_child_grid', [
				'model' => $model,
			]) ?>
		</div>
	</div>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rbac', 'Create') : Yii::t('rbac', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
