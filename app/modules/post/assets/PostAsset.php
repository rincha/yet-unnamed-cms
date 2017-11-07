<?php

namespace app\modules\post\assets;

use yii\web\AssetBundle;

/**
 * @author rincha
 */

class PostAsset extends AssetBundle {

    public $sourcePath='@app/modules/post/assets/source';
    public $css = [
        'post.css',
    ];
    public $js = [];
}
