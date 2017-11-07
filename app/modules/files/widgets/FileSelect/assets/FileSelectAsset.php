<?php
namespace app\modules\files\widgets\FileSelect\assets;

use yii\web\AssetBundle;

class FileSelectAsset extends AssetBundle
{
    public $sourcePath='@app/modules/files/widgets/FileSelect/assets/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'filesbrowser.css',
    ];
    public $js = [
        'filesbrowser.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
