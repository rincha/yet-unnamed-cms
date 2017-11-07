<?php

namespace app\assets\BxSlider;

use yii\web\AssetBundle;

/**
 * Copyright (c) 2016-2017 rincha
 * @author rincha
 * @license MIT, For the full copyright and license information, please view the LICENSE
 */

class BxSliderAsset extends AssetBundle {

    public $sourcePath = '@app/assets/BxSlider/source';
    public $publishOptions = ['forceCopy' => false];
    public $css = [
        'jquery.bxslider.css',
    ];
    public $js = [
        'jquery.bxslider.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
