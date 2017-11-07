<?php
namespace app\common\widgets\FileInput\assets;

use yii\web\AssetBundle;

class FileInputAsset extends AssetBundle
{
    public $sourcePath='@app/common/widgets/FileInput/assets/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'default.css',
    ];    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
