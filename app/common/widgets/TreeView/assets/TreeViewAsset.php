<?php
namespace app\common\widgets\TreeView\assets;

use yii\web\AssetBundle;

class TreeViewAsset extends AssetBundle
{
    public $sourcePath='@app/common/widgets/TreeView/assets/source';
    public $publishOptions=['forceCopy'=>false];
    public $css = [
        'treeview.css',
    ];
    public $js = [
        'treeview.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
