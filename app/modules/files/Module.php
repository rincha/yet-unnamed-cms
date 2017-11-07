<?php

namespace app\modules\files;

use Yii;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\files\controllers';
    public $allowedExts = 'jpg, jpeg, gif, png, flv, mp3, mp4, avi, swf, txt, rtf, doc, docx, odt, csv, xls, xlsx, xml, ods, pdf, zip, gz, tar, ppt, pps, pptx, ppsx, odp, psd, xcf';
    public $allowedImagesExts = 'jpg, jpeg, gif, png';
    public $maxFileSize=8388608;//8Mb
    public $migrations = true;
    public $urlPath = 'files';
    public $path;
    public $dirMode = 0777;

    public function getLinksDefinition() {
        return [
            'moduleName'=>Yii::t('files', 'Files'),
            'controllers'=>include __DIR__.DIRECTORY_SEPARATOR.'controllers.php',
        ];
    }

    public function init() {
        parent::init();
        if (!$this->path) {
            $this->path = \Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $this->urlPath;
        }
        // custom initialization code goes here
    }

    public function safeInstall() {
        if (!file_exists($this->path) || !is_dir($this->path)) {
            if (!mkdir($this->path, $this->dirMode)) {
                throw new Exception('Can`t create directory: '.$this->path);
            }
            else {
                chmod($this->path, $this->dirMode);
            }
        }
    }

}
