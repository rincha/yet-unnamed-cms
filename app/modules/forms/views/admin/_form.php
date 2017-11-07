<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\forms\models\Form */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?=

    $form->field($model, 'description')->widget(\app\common\widgets\tinymce\Tinymce::className(), [
            'config' => app\common\helpers\AppHelper::getTinyMceConfig(),
    ]); ?>

    <?= $form->field($model, 'type')->dropDownList(\app\modules\forms\models\Form::getTypes()) ?>

    <?= $form->field($model, 'emails')->textInput(['maxlength' => 512]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => 11]) ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <?= $form->field($model, 'button')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

$this->registerJs('$("#'.Html::getInputId($model, 'title').'").on("change",function(){'
        . 'console.log("change");'
        . 'if (!$("#'.Html::getInputId($model, 'name').'").attr("data-value")) {'
        . '$("#'.Html::getInputId($model, 'name').'").val(adminApi.safeStr($(this).val(),"_"));'
        . '}'
        . '});');