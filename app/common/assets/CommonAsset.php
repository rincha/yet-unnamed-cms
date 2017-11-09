<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\assets;

use yii\web\AssetBundle;

class CommonAsset extends AssetBundle
{
    public $sourcePath='@app/common/assets/source';
    public $js = [
        'bootstrap-show-password/bootstrap-show-password.min.js',
    ];
    public $css=[
        'font-awesome-4.7.0/css/font-awesome.min.css',
        'default/default.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
