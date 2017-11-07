<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\forms\models\FormField */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'type_id')->dropDownList($model->getTypeList(),[]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>
        </div>
    </div>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'sort_order')->textInput(['maxlength' => 32]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'required')->checkbox([]) ?>
        </div>
    </div>

    <?= $form->field($model, 'params')->textInput(['maxlength' => 512]) ?>

    <?= $form->field($model, 'options')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'tip')->textarea(['rows' => 2]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
