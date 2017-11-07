<?php

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath='@app/modules/admin/assets/source';
    //public $publishOptions=['forceCopy'=>true];
    public $css = [
        'css/admin.css',
    ];
    public $js = [
        'js/admin.js',
    ];
    public $depends = [
        //'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\common\assets\CommonAsset',
        'app\assets\ImagePopup\ImagePopupAsset'
    ];
}
