<?php
namespace app\common\widgets\FileInput;

use Yii;
use yii\helpers\Html;
use app\common\widgets\FileInput\assets\FileInputAsset;

class FileInput extends \yii\widgets\InputWidget {

    public $browseLabel=null;
    public $browseButtonOptions=['class'=>'btn btn-primary btn-file'];

    public $groupOptions=['class'=>'input-group file-input-group',];

    public $textInputName=null;
    public $textInputValue=null;
    public $textInputOptions=['readonly'=>'', 'class'=>'form-control'];

    public $changeButton=false;
    public $changeButtonLabel=null;
    public $changeButtonCheck=false;

    public function run() {
        if ($this->changeButton===true) {
            $changeButton=  Html::tag(
                    'span',
                    Html::checkbox(null,  $this->changeButtonCheck,['class'=>'file-input-group-change','title'=>$this->changeButtonLabel]),
                    ['class'=>'input-group-addon']
            );
            $this->options['disabled']=!$this->changeButtonCheck;
            $this->textInputOptions['disabled']=!$this->changeButtonCheck;
        }
        else {
            $changeButton='';
        }

        if (!isset($this->textInputOptions['data-text-multiple'])) {
            $this->textInputOptions['data-text-multiple']=Yii::t('app','files selected: {n}');
        }
        $textInput=  Html::textInput($this->textInputName,$this->textInputValue,$this->textInputOptions);
        if ($this->browseLabel===null) {
            $this->browseLabel=Yii::t('app','Browse...');
        }
        if ($this->hasModel()) {
            $fileInput=Html::activeFileInput($this->model, $this->attribute, $this->options);
        }
        else {
            $fileInput=Html::fileInput($this->name, $this->value, $this->options);
        }
        $browseButton=Html::tag('span',$this->browseLabel.''.$fileInput,$this->browseButtonOptions);
        $this->registerJs();
        return Html::tag('div',
                Html::tag('span',$browseButton,['class'=>'input-group-btn']).
                $textInput.$changeButton,
                $this->groupOptions
        );
    }

    private function registerJs() {
        FileInputAsset::register($this->view);
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
        });
        ");
        if ($this->changeButton) {
            $this->view->registerJs(
                    '$(".file-input-group-change").on("change click",function(){'
                    . '$(this).parents(".file-input-group:first").find(\'input[type="file"], input[type="text"], input[type="hidden"]\').attr("disabled",!$(this).is(":checked"))'
                    . '});'
            );
        }
    }
}
