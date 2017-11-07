<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Copyright (c) 2016-2017 rincha
 * @author rincha
 * @license MIT, For the full copyright and license information, please view the LICENSE
 */

class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [];
    public $forceCopy = false;
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
