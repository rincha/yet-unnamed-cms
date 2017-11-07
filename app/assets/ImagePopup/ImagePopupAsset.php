<?php
namespace app\assets\ImagePopup;

/**
 * Copyright (c) 2016-2017 rincha
 * @author rincha
 * @license MIT, For the full copyright and license information, please view the LICENSE
 */

use yii\web\AssetBundle;

class ImagePopupAsset extends AssetBundle
{
    public $sourcePath='@app/assets/ImagePopup/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'js/magnific-popup.css',
    ];
    public $js = [
        'js/jquery.magnific-popup.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * string|null|false - set to false for no auto append plugin
     */
    public $selector=null;

    /**
     * plugin options
     */
    public $pluginOptions=null;

    /**
     * Registers the CSS and JS files with the given view.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {

        if ($this->selector!==false) {
            $selector=$this->selector===null?'a.img-zoom':$this->selector;
            $pluginOptions=$this->pluginOptions===null?[
                'type'=>'image',
                'image'=>['verticalFit'=>false],
            ]:$this->pluginOptions;
            $view->registerJs('$("'.$selector.'").magnificPopup('.(\yii\helpers\Json::encode($pluginOptions)).');');
        }
        return parent::registerAssetFiles($view);
    }
}
