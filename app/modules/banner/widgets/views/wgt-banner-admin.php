<?php

use app\modules\gb\models\Category;

/* @var $this yii\web\View */
/* @var $this->context app\modules\banner\widgets\SiteWgtBanner */
/* @var $model app\models\Widget */
/* @var $form yii\widgets\ActiveForm */


?>

<?php
$options=[];
if ($model->hasErrors('options[banner]')) {
    $options['class']=$form->errorCssClass;
}
echo $form->field($model, 'options[banner]',['options'=>$options])->widget(
            app\common\widgets\AutocompleteWithId::className(),
                [
                    'viewValue'=>$this->context->getBanner()?$this->context->getBanner()->name:null,
                    'allowCustomValue'=>false,
                    'clientOptions'=>[
                        'source'=>  \yii\helpers\Url::to(['/admin/banner/lookup']),
                    ],
                    'options'=>['class'=>'form-control']
                ]
            )->label($this->context->optionsAttributes['banner']['label'])->error();
$options=[];
if ($model->hasErrors('options[thumbnail]')) {
    $options['class']=$form->errorCssClass;
}
echo $form->field($model, 'options[thumbnail]')
        ->textInput([])
        ->label($this->context->optionsAttributes['thumbnail']['label'])
        ->hint($this->context->optionsAttributes['thumbnail']['hint']);
?>