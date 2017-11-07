<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\forms\models\FormField;
use yii\captcha\Captcha;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $form app\modules\forms\models\Form */
/* @var $model app\modules\forms\models\FormSend */

$this->title = $form->title;
$this->params['breadcrumbs'][] = $form->title;

?>
<div class="form-view">

    <h1><?= Html::encode($form->title) ?></h1>
    <?= $form->description ?>

    <?php $activeform = ActiveForm::begin([

    ]); ?>

    <?php
    foreach ($form->fields as $field) {
        switch ($field->type_id) {
            case FormField::TYPE_STRING:
                echo $activeform->field($model, $field->name)->textInput(['maxlength' => 255]);
                break;
            case FormField::TYPE_CHECKBOX:
                echo $activeform->field($model, $field->name)->checkbox([]);
                break;
            case FormField::TYPE_EMAIL:
                echo $activeform->field($model, $field->name)->textInput(['maxlength' => 255]);
                break;
            case FormField::TYPE_INTEGER:
                echo $activeform->field($model, $field->name)->textInput(['type' => 'number']);
                break;
            case FormField::TYPE_NUMERIC:
                echo $activeform->field($model, $field->name)->textInput(['type' => 'number']);
                break;
            case FormField::TYPE_TEXT:
                echo $activeform->field($model, $field->name)->textarea(['rows' => 3]);
                break;
            case FormField::TYPE_MASK:
                echo $activeform->field($model, $field->name, ['enableClientValidation' => false])->widget(
                        MaskedInput::className(),
                        [
                            'mask'=>$field->getParam('mask'),
                            'options'=>['class'=>'form-control']
                        ]
                );
                break;
            case FormField::TYPE_SELECT:
                echo $activeform->field($model, $field->name)->dropDownList($field->getOptionsArr());
                break;
            case FormField::TYPE_CAPTCHA:
                echo $activeform->field($model, $field->name, ['enableClientValidation' => false])->widget(
                        Captcha::className(),
                        ['captchaAction' => '/site/captcha']
                );
                break;
        }
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton(Html::encode($form->button?$form->button:Yii::t('forms', 'Send')), ['class' => 'btn btn-success' ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
