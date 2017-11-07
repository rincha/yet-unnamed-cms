<?php

namespace app\common\widgets\MultiSelect;

use yii\helpers\Html;
use yii\helpers\Json;
use app\common\widgets\MultiSelect\assets\MultiSelectAsset;
/**
 * @author rincha
 */
class MultiSelect extends \yii\widgets\InputWidget {

    public $items;
    public $settings=[];

    public function run() {
        $this->options['id']=$this->id;
        if (!isset($this->options['class'])) {
            $this->options['class']='form-control';
        }
        else {
            $this->options['class'].=' form-control';
        }
        if ($this->hasModel()) {
            $input=Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        }
        else {
            $input=Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
        MultiSelectAsset::register($this->view);

        $this->view->registerJs('$("#'.$this->id.'").multiselect('.Json::encode($this->settings).');');
        return $input;
    }

}
