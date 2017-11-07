<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Folder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="folder-form">

    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]);?>

    <?= $form->field($model, 'parent_id')->dropDownList([''=>'']+$model->getListArray()) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'pathname')->textInput(['maxlength' => 255, 'data-value'=>$model->pathname]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'type')->dropDownList(app\modules\files\models\Folder::getTypeList()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app',  $model->isNewRecord ? 'Create' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

