<?php
namespace app\common\widgets\MiniColors\assets;

use yii\web\AssetBundle;

class MiniColorsAsset extends AssetBundle
{
    public $sourcePath='@app/common/widgets/MiniColors/assets/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'jquery.minicolors.css',
    ];
    public $js = [
        'jquery.minicolors.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
