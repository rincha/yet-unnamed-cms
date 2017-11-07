<?php
namespace app\common\widgets\FileInputGroup\assets;

use yii\web\AssetBundle;

class FileInputGroupAsset extends AssetBundle
{
    public $sourcePath='@app/common/widgets/FileInputGroup/assets/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'default.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
