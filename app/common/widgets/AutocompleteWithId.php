<?php

namespace app\common\widgets;

use yii\helpers\Html;
/**
 *
 * @author rincha
 */
class AutocompleteWithId extends \yii\jui\AutoComplete {

    public $viewName='fake';
    public $viewValue;

    public $allowCustomValue=false;

    //public $enableNull=false;

    public function run() {

        echo $this->renderWidget();
        if ($this->hasModel()) {
            $fakeid=  'fake'.'-'.Html::getInputId($this->model, $this->attribute);
            $id=Html::getInputId($this->model, $this->attribute);
        }
        else {
            throw new \yii\base\Exception('Only active field');
        }
        $this->clientOptions['select']=new \yii\web\JsExpression('function(event, ui){'
                . '$("#'.$id.'").val(ui.item.value);'
                . '$("#'.$id.'").attr("data-label",ui.item.label);'
                . '$("#'.$fakeid.'").val(ui.item.label);'
                . '$("#'.$id.'").change();'
                . 'event.preventDefault();'
                . '}');

        if (!$this->allowCustomValue) {
            $this->clientOptions['change']=new \yii\web\JsExpression('function(event, ui){'
                . 'if ($("#'.$fakeid.'").val()!=$("#'.$id.'").attr("data-label")) {'
                . (!$this->allowCustomValue?'$("#'.$fakeid.'").val("");':'')
                . '$("#'.$id.'").val("");'
                . '$("#'.$id.'").change();'
                . '}'
                . '}');
        }

        $this->registerWidget('autocomplete',$fakeid);
    }

    public function renderWidget()
    {
        if ($this->hasModel()) {
            $aoptions=$this->options;
            $aoptions['value']=$this->viewValue;
            $aoptions['name']=$this->viewName;
            $aoptions['id']='fake'.'-'.Html::getInputId($this->model, $this->attribute);
            return Html::activeTextInput($this->model, $this->attribute, $aoptions).
                   Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            return Html::textInput($this->viewName, $this->value, $this->options).
                   Html::hiddenInput($this->name, $this->value, $this->options);
        }
    }

}
