<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $this->context app\common\widgets\SiteBxSlider\SiteBxSlider */
/* @var $model app\models\Widget */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('app/widgets', 'Slider settings')?></div>
    <div class="panel-body">
<?php
$n=0;
$rowStart=false;
foreach ($this->context->getOptionsAttributes() as $k=>$v) {
    if (strpos($k, 'opt_')===0) {
        if ($n===0) {
            echo '<div class=row>';
            $rowStart=true;
        }
        elseif ($n%3===0) {
            echo '</div><div class=row>';
            $rowStart=true;
        }
        echo '<div class="col-xs-4">';
        $options=[];
        if ($model->hasErrors('options['.$k.']')) {
            $options['class']=$form->errorCssClass;
        }
        $fieldOptions=[];
        if ($model->isNewRecord && !isset($model->options[$k])) {
            $fieldOptions['value']=$this->context->optionsAttributes[$k]['defaultValue'];
        }
        else {
            $fieldOptions['value']=$this->context->getOption($k);
        }
        echo $form->field($model, 'options['.$k.']',['options'=>$options])
                ->textInput($fieldOptions)
                ->label($this->context->optionsAttributes[$k]['label'])
                ->hint($this->context->optionsAttributes[$k]['hint']);
        echo '</div>';
        $n++;
    }
}
if ($rowStart) {echo '</div>';};
?>
    </div>
</div>


<div class="row">
<?php
$options=[];
if ($model->hasErrors('options[images]')) {
    $options['class']=$form->errorCssClass;
}
$images=$this->context->getOption('images');;
/*if (is_array($images)) {
    $images=implode(',', $images);
}*/
for ($i=0; $i<8; $i++) {
    ?>
    <div class="col-md-6">
    <div class="panel panel-default"><div class="panel-body">
    <?php
    echo $form->field($model, 'options[images]['.$i.']',['options'=>$options])->widget(
                app\modules\files\widgets\FileSelect\FileSelectInput::className(),
                [
                    'name'=>Html::getInputName($model, 'options[images]['.$i.']'),
                    'value'=>ArrayHelper::getValue($images, $i,''),
                    'inputOptions'=>['class'=>'input-sm form-control'],
                    'browseButtonOptions'=>['class'=>'btn btn-primary btn-file btn-sm'],
                ]
            )->label($this->context->optionsAttributes['images']['label'].' #'.($i+1));

    echo $form->field($model, 'options[links]['.$i.']')->textInput(['class'=>'input-sm form-control'])->label($this->context->optionsAttributes['links']['label'].' #'.($i+1));

    echo $form->field($model, 'options[titles]['.$i.']')->textInput(['class'=>'input-sm form-control'])->label($this->context->optionsAttributes['titles']['label'].' #'.($i+1));

    echo $form->field($model, 'options[descriptions]['.$i.']')->textInput(['class'=>'input-sm form-control'])->label($this->context->optionsAttributes['descriptions']['label'].' #'.($i+1));

    ?>
    </div></div>
    </div>
    <?php
}
?>
</div>