<?php

namespace app\modules\forms;
use Yii;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\forms\controllers';
    public $installed = false;
    public $migrations=true;
    public $tinyMceConfig = [ // Read more: http://www.tinymce.com/wiki.php/Configuration
        'language' => 'ru_RU',
        'language_url' => '/js/tinymce/ru.js',
        'browser_spellcheck' => true,
        'menu' => [],
        'document_base_url' => null,
        'relative_urls' => true,
        'plugins' => "rfiles, hr,link,image,charmap,paste,print,preview,anchor,pagebreak,spellchecker,searchreplace,visualblocks,visualchars,code,fullscreen,insertdatetime,media,nonbreaking,save,table,directionality,template,textcolor",
        'toolbar' => [
            "pastetext | undo redo searchreplace | removeformat fullscreen preview code visualchars visualblocks | hr charmap",
            "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect fontsizeselect | forecolor backcolor",
            "bullist numlist outdent indent | link unlink anchor | subscript superscript | table | media image | styleselect | rfiles",
        ],
        'fontsize_formats' => "8px 10px 12px 16px 18px 20px 24px 28px 32px",
    ];

    public function getLinksDefinition() {
        return [
            'moduleName' => Yii::t('forms', 'Forms'),
            'controllers' => include __DIR__ . DIRECTORY_SEPARATOR . 'controllers.php',
        ];
    }

    public function init() {
        parent::init();

        // custom initialization code goes here
    }

}
