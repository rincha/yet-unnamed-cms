<?php

namespace app\common\widgets\tinymce;

class TinymceAssets extends \yii\web\AssetBundle
{
	public $sourcePath = '@app/common/widgets/tinymce/tinymce';
	public $js = [
		'tinymce.min.js',
	];
        public $css = [
            'editor.css',
        ];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\web\JqueryAsset',
                'yii\bootstrap\BootstrapAsset',
                'app\common\assets\CommonAsset',
	];
	public $publishOptions=['forceCopy'=>false];

        public function publish($am) {
            if (file_exists($am->getPublishedPath($this->sourcePath).DIRECTORY_SEPARATOR.'changed')) {
                $source_time=filemtime(\Yii::getAlias($this->sourcePath.DIRECTORY_SEPARATOR.'changed'));
                $publish_time=filemtime($am->getPublishedPath($this->sourcePath).DIRECTORY_SEPARATOR.'changed');
                if ($source_time && $publish_time && $source_time>$publish_time) {
                    $this->publishOptions['forceCopy']=true;
                }
            }
            parent::publish($am);
        }

}
