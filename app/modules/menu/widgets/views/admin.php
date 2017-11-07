<?php
/* @var $this yii\web\View */
/* @var $this->context app\modules\menu\widgets\SiteWgtMenu */
/* @var $model app\models\Widget */
/* @var $form yii\widgets\ActiveForm */


?>

<?php
$options=[];
if ($model->hasErrors('options[menu]')) {
    $options['class']=$form->errorCssClass;
}
echo $form->field($model, 'options[menu]',['options'=>$options])->widget(
            app\common\widgets\AutocompleteWithId::className(),
                [
                    'viewValue'=>$this->context->getMenu()?$this->context->getMenu()->name:null,
                    'allowCustomValue'=>false,
                    'clientOptions'=>[
                        'source'=>  \yii\helpers\Url::to(['/menu/admin/lookup']),
                    ],
                    'options'=>['class'=>'form-control']
                ]
            )->label($this->context->optionsAttributes['menu']['label'])->error();
$options=[];
if ($model->hasErrors('options[cssClass]')) {
    $options['class']=$form->errorCssClass;
}
echo $form->field($model, 'options[cssClass]')
        ->textInput([])
        ->label($this->context->optionsAttributes['cssClass']['label']);
?>