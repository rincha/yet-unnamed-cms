<?php

use yii\helpers\ArrayHelper;
use app\modules\info\models\Type;

/* @var $this yii\web\View */
/* @var $this->context app\modules\menu\widgets\SiteWgtNews */
/* @var $model app\models\Widget */
/* @var $form yii\widgets\ActiveForm */


?>

<?php
$options=[];
if ($model->hasErrors('options[type]')) {
    $options['class']=$form->errorCssClass;
}
$items=[''=>'']+ArrayHelper::map(Type::find()->all(), 'type_id', 'title');
echo $form->field($model, 'options[type]',['options'=>$options])->dropDownList($items)->label($this->context->optionsAttributes['type']['label'])->hint($this->context->optionsAttributes['type']['hint']); 


$options=[];
if ($model->hasErrors('options[count]')) {
    $options['class']=$form->errorCssClass;
}
echo $form->field($model, 'options[count]')
        ->textInput([])
        ->label($this->context->optionsAttributes['count']['label']); 

$options=[];
if ($model->hasErrors('options[more_text]')) {
    $options['class']=$form->errorCssClass;
}
echo $form->field($model, 'options[more_text]')
        ->textInput([])
        ->label($this->context->optionsAttributes['more_text']['label']);
?>