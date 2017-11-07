<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\files\widgets\FileSelect\FileSelectInput;
use app\common\widgets\tinymce\Tinymce;
use app\common\widgets\MiniColors\MiniColorsInput;

/* @var $this yii\web\View */
/* @var $model app\modules\promo\models\PromoBlock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promo-block-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'status')->dropDownList($model->statusList()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'background_color')->widget(MiniColorsInput::className()) ?>
        </div>
        <div class="col-sm-6">

            <?= $form->field($model, 'background_image')->widget(FileSelectInput::className(),[
                    ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'content')->widget(Tinymce::className(),[
        'config'=>  \app\common\helpers\AppHelper::getTinyMceConfig(),
        'options'=>['style'=>'height:400px;']
    ]) ?>

    <?= $form->field($model, 'script')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'style')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'params')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
