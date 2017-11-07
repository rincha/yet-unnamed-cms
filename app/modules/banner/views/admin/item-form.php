<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\modules\files\widgets\FileSelect\FileSelectInput;

/* @var $this yii\web\View */
/* @var $model app\modules\banner\models\BannerItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="banner-item-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'image')->widget(FileSelectInput::className(),[
  
    ]) ?>
    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'start_at')->widget(MaskedInput::className(),[
                'mask' => '9999-99-99',
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'end_at')->widget(MaskedInput::className(),[
                'mask' => '9999-99-99',
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'text')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
