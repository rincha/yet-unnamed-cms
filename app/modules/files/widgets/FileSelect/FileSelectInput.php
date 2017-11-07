<?php
namespace app\modules\files\widgets\FileSelect;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use app\modules\files\widgets\FileSelect\assets\FileSelectAsset;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
/**
 *
 * @author rincha
 */
class FileSelectInput extends InputWidget {
           
    public $browseLabel=null;
    public $browseButtonOptions=['class'=>'btn btn-primary btn-file'];
    
    public $name;
    public $value;
    public $pathValue;
    public $inputOptions=[];
    
    public $multiple=false;
    public $multipleCount=0;
    
    public $fileSelectOptions=[];

    public function run() {
        if ($this->hasModel() && $this->name===null) {
            $this->name=  Html::getInputName($this->model, $this->attribute);
        }
        if ($this->hasModel() && $this->value===null) {
            $this->value=$this->model->{$this->attribute};
        }
        if (isset($this->inputOptions['class'])) {
            $this->inputOptions['class'].=' form-control';
        }
        else {
            $this->inputOptions['class']='form-control';
        }
        if ($this->multiple && !is_array($this->value)) {
            throw new \yii\base\Exception('multiple FileSelectInput, must have property [value] like array');
        }
        if ($this->multiple && $this->multipleCount<2) {
            throw new \yii\base\Exception('multipleCount for FileSelectInput, must be more then 1');
        }
        
        if ($this->browseLabel===null) {
            $this->browseLabel=Yii::t('app','Browse...');
        }
        
        FileSelectAsset::register($this->view);
        $defaultOptions=[
            'initEl'=>'#'.$this->getId().' .btn',
            'foldersGetUrl'=>Url::to(['/admin/files/api-get-folders']),
            'folderCreateUrl'=>Url::to(['/admin/files/api-create-folder']),
            'folderDeleteUrl'=>Url::to(['/admin/files/api-delete-folder']),
            'folderVarName'=>'Folder[title]',
            'filesGetUrl'=>Url::to(['/admin/files/api-get-files']),
            'filesCreateUrl'=>Url::to(['/admin/files/api-create-file']),
            'filesDeleteUrl'=>Url::to(['/admin/files/api-delete-file']),
            'csrfTokenName'=>  Yii::$app->request->csrfParam,
            'csrfToken'=>  Yii::$app->request->csrfToken,
            'foldersParent'=>true,
            'thumbnails'=>[
                    'enabled'=>true,
                    'extensions'=>['jpg','png','gif'],
                    'thumbnail'=>['ExactFit',0],
                    'sizes'=>  \Yii::$app->params['images']['thumbnails'],
            ],
            'lang'=>  explode('-', Yii::$app->language)[0],
        ];
        $options=  array_merge($defaultOptions,$this->fileSelectOptions);
        if ($this->multiple) {
            return $this->renderTextMultiple($options);
        }
        else {
            return $this->renderText($options);
        }
    }
    
    public function renderText($options) {
        $browseButton=Html::tag('span',$this->browseLabel,$this->browseButtonOptions);     
        
        $this->view->registerJs('$("#'.$this->getId().' input").filesBrowser('.Json::encode($options).');');
        
        $input=Html::textInput($this->name,$this->value,$this->inputOptions);
        
        return Html::tag('div',
            $input.
            Html::tag('span',$browseButton,['class'=>'input-group-btn']),
            ['class'=>'input-group','id'=>  $this->getId()]
        );
    }
    
    public function renderTextMultiple($options) {
        $groups=[];
        for ($i=0; $i<$this->multipleCount; $i++) {
            $input=Html::textInput(
                    $this->name,
                    \yii\helpers\ArrayHelper::getValue($this->value, $i),
                    array_merge($this->inputOptions,['data-selector'=>$this->getId().'-input-'.$i])
            );
            $browseButton=  Html::tag('span',$this->browseLabel,array_merge($this->browseButtonOptions,['data-selector'=>$this->getId().'-btn-'.$i]));
            $options['initEl']='#'.$this->getId().' .btn[data-selector="'.$this->getId().'-btn-'.$i.'"]';
            $this->view->registerJs('$(\'#'.$this->getId().' input[data-selector="'.$this->getId().'-input-'.$i.'"]\').filesBrowser('.Json::encode($options).');');
            $groups[]=Html::tag('div',
                $input.
                Html::tag('span',$browseButton,['class'=>'input-group-btn']),
                ['class'=>'input-group','style'=>'margin:3px 0;']
            );
        }
        return Html::tag('div',  implode('', $groups),['id'=>  $this->getId()]);
    }
    
}
