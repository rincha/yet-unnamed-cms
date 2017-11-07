<?php
namespace app\common\widgets\MultiSelect\assets;

use yii\web\AssetBundle;

class MultiSelectAsset extends AssetBundle
{
    public $sourcePath='@app/common/widgets/MultiSelect/assets/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'css/bootstrap-multiselect.css',
    ];
    public $js = [
        'js/bootstrap-multiselect.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
