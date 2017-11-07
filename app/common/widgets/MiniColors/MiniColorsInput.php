<?php
namespace app\common\widgets\MiniColors;

use yii\helpers\Html;
use yii\helpers\Json;
use app\common\widgets\MiniColors\assets\MiniColorsAsset;
/**
 * Description of MiniColorsInput
 *
 * @author rincha
 */
class MiniColorsInput extends \yii\widgets\InputWidget {

    public $settings=[
        'theme'=>'bootstrap',
    ];

    public function run() {
        $this->options['id']=$this->id;
        if (!isset($this->options['class'])) {
            $this->options['class']='form-control';
        }
        else {
            $this->options['class'].=' form-control';
        }
        if ($this->hasModel()) {
            $input=Html::activeTextInput($this->model, $this->attribute, $this->options);
        }
        else {
            $input=Html::textInput($this->name, $this->value, $this->options);
        }
        MiniColorsAsset::register($this->view);
        $this->view->registerJs('$("#'.$this->id.'").minicolors('.Json::encode($this->settings).');');
        return $input;
    }
}
