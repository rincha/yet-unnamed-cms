<?php
namespace app\common\widgets\FileInputGroup;

use Yii;
use yii\helpers\Html;
use app\common\widgets\FileInputGroup\assets\FileInputGroupAsset;

/*
need css 
*/

class FileInputGroup extends \yii\base\Widget {
    
    public $browseLabel=null;
    public $browseButtonOptions=['class'=>'btn btn-primary btn-file'];
    
    public $fileInputName='Filedata';
    public $fileInputOptions=[];
    
    public $textInputName=null;
    public $textInputOptions=['readonly'=>'', 'class'=>'form-control'];
    
    public $submitButton=false;
    
    public function run() {
        $fileInput=  Html::fileInput($this->fileInputName,'',$this->fileInputOptions);
        
        if (!isset($this->textInputOptions['data-text-multiple'])) {
            $this->textInputOptions['data-text-multiple']=Yii::t('app','files selected: {n}');
        }
        $textInput=  Html::textInput($this->textInputName,'',$this->textInputOptions);
        
        if ($this->browseLabel===null) {
            $this->browseLabel=Yii::t('app','Browse...');
        }
        $browseButton=  Html::tag('span',$this->browseLabel.''.$fileInput,$this->browseButtonOptions);
        
        if ($this->submitButton===true) {
            $submitButton=  Html::tag(
                    'span',
                    Html::submitButton(\yii\bootstrap\Html::icon('upload').' '.Yii::t('app','Upload'), ['class'=>'btn btn-success']),
                    ['class'=>'input-group-btn']
            );
        }
        elseif ($this->submitButton===false) {
            $submitButton='';
        }
        else {
            $submitButton=  Html::tag(
                    'span',
                    $this->submitButton,
                    ['class'=>'input-group-btn']
            );
        }
        FileInputGroupAsset::register($this->view);
        $this->view->registerJs("$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
var caption = $(this).parents('.input-group:first').find('input[type=\"text\"]');
var text='';
if (numFiles>1) {
    text=$(caption).attr('data-text-multiple');
    text=text.replace(/{n}/i,numFiles);
}
else {
    text=label;
}
$(caption).val(text);
});
$(document).on('change', '.btn-file :file', function() {
var input = $(this),
numFiles = input.get(0).files ? input.get(0).files.length : 1,
label = input.val();
input.trigger('fileselect', [numFiles, label]);
});");
        return Html::tag('div',
                Html::tag('span',$browseButton,['class'=>'input-group-btn']).
                $textInput.$submitButton,
                ['class'=>'input-group',]
        );
    }
}
