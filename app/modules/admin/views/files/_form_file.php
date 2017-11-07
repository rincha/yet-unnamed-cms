<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\File */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="file-form">

    <?php 
    $form = ActiveForm::begin([
        'enableClientValidation'=>false,
        'enableAjaxValidation'=>false,
        'options'=>['enctype'=>'multipart/form-data']
    ]); 
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    
    <?= $form->field($model, 'pathname')->textInput(['maxlength' => 255]) ?>
    
    <div class="form-group">
    <?= Html::label($model->getAttributeLabel('new_file'), Html::getInputName($model, 'new_file')) ?>
    
    <?= app\common\widgets\FileInputGroup\FileInputGroup::widget([
        'fileInputName'=>Html::getInputName($model, 'new_file'),
    ]) ?>  
    
    <?= Html::error($model,'new_file') ?>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app',  $model->isNewRecord ? 'Create' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
