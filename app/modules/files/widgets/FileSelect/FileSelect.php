<?php
namespace app\modules\files\widgets\FileSelect;

use yii\base\Widget;
use app\modules\files\widgets\FileSelect\assets\FileSelectAsset;
/**
 *
 * @author rincha
 */
class FileSelect extends Widget {
    
    public $selector='.file-select';
    public $options=[];
    
    public function run() {
        FileSelectAsset::register($this->view);
        $options=new \yii\web\JsExpression($this->options);
        $this->view->registerJs('$("'.$this->selector.'").filesBrowser('.$options.');');
        return '';
    }
    
}
