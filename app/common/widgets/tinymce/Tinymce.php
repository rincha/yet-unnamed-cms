<?php

namespace app\common\widgets\tinymce;

use yii\helpers\Html;
use yii\helpers\Json;

class Tinymce extends \yii\widgets\InputWidget {

    public $config = [];

    /**
     * Initializes the widget.
     */
    public function init() {

    }

    public static function getDefaultConfig(\yii\web\View $view) {
        TinymceAssets::register($view);
        $tinymce_path=$view->assetBundles['app\common\widgets\tinymce\TinymceAssets']->sourcePath;
        $tinymce_url=$view->assetManager->getPublishedUrl($tinymce_path);
        $external_plugins_url=$view->assetManager->getPublishedUrl($tinymce_path).DIRECTORY_SEPARATOR.'external-plugins';
        $bootstrap_path=$view->assetBundles['yii\bootstrap\BootstrapAsset']->sourcePath;
        $bootstrap_url=$view->assetManager->getPublishedUrl($bootstrap_path);
        $jquery_path=$view->assetBundles['yii\web\JqueryAsset']->sourcePath;
        $jquery_url=$view->assetManager->getPublishedUrl($jquery_path);
        $default=[
            'element_format'=>'html',
            'relative_urls'=>false,
            'convert_urls'=>false,
            'entity_encoding'=>'named',
            'language'=>  explode('-',\Yii::$app->language)[0],
            'convert_fonts_to_spans'=>true,
            'fullscreen_new_window'=>true,
            'plugins'=>explode(',','template,hr,link,lists,image,charmap,paste,print,preview,anchor,pagebreak,spellchecker,searchreplace,visualblocks,visualchars,code,fullscreen,insertdatetime,media,nonbreaking,save,table,directionality,template,textcolor'),
            'menu'=>[],
            'spellchecker_language'=>'English=en,Russian=ru',
            'browser_spellcheck'=>true,
            'toolbar'=>[
                "pastetext | undo redo searchreplace | spellchecker removeformat fullscreen preview code visualchars visualblocks | hr charmap | template",
                "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect fontsizeselect | forecolor backcolor",
                "bullist numlist outdent indent | link unlink anchor | subscript superscript | table | media image rfiles | styleselect | style"
            ],
            //'extended_valid_elements'=>'@[itemscope|itemtype|itemid|itemprop|content|*],div,span,a,p',
            'verify_html'=>false,
            'content_css'=>[
                $bootstrap_url.'/css/bootstrap.min.css',
                $tinymce_url.'/content.css'
            ],
            //external plugins specific
            'external_plugins'=>[
                'style'=>$external_plugins_url.DIRECTORY_SEPARATOR.'style'.DIRECTORY_SEPARATOR.'plugin.min.js',
                'rfiles'=>$external_plugins_url.DIRECTORY_SEPARATOR.'rfiles'.DIRECTORY_SEPARATOR.'plugin.min.js',
                'rlink'=>$external_plugins_url.DIRECTORY_SEPARATOR.'rlink'.DIRECTORY_SEPARATOR.'plugin.min.js',
                'pastehtml'=>$external_plugins_url.DIRECTORY_SEPARATOR.'pastehtml'.DIRECTORY_SEPARATOR.'plugin.min.js',
            ],
            'bootstrap'=>[
                'css'=>$bootstrap_url.'/css/bootstrap.min.css',
                'js'=>$bootstrap_url.'/js/bootstrap.min.js',
            ],
            'jquery_url'=>$jquery_url.DIRECTORY_SEPARATOR.'jquery.js',
            'rfiles'=>[
                'thumbnails'=>[
                    'enabled'=>false,
                    'extensions'=>['jpg'],
                    'sizes'=>[
                        'default'=>['50x50'],
                    ]
                ],
                'actions'=>[
                    'getFolders'=>null,
                    'getFiles'=>null,
                    'deleteFile'=>null,
                    'deleteFolder'=>null,
                    'createFolder'=>null,
                    'createFile'=>null,
                    'updateFile'=>null,
                ],
                'additionalFormsParams'=>[],
            ],
        ];
        return $default;
    }

    private function makeEditorOptions() {
        return array_merge(
                self::getDefaultConfig($this->view),
                $this->config
        );
    }

    /**
     * Renders the widget.
     */
    public function run() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
        }
        $this->config['selector'] = 'textarea#' . $this->options['id'];
        $this->getView()->registerJs(
                'tinymce.init(' . Json::encode($this->makeEditorOptions()) . ');'
        );
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        }
        else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
    }

}
