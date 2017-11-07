<?php
namespace app\common\widgets\SiteBxSlider\assets;

use yii\web\AssetBundle;

class SiteBxSliderAsset extends AssetBundle
{
    public $sourcePath='@app/common/widgets/SiteBxSlider/assets/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'jquery.bxslider.yrcms.css',
    ];  
    public $js = [
        'jquery.bxslider.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
