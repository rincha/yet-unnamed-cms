<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\promo\models\Promo */

$this->title=$model->meta_title?$model->meta_title:$model->name;
if ($model->meta_description) {
    $this->registerMetaTag(['name'=>'description','content'=>$model->meta_description]);
}
if ($model->keywords) {
    $this->registerMetaTag(['name'=>'keywords','content'=>$model->keywords]);
}

$n=0;
foreach ($model->blocks as $block) {
    if ($block->style) {
        $this->registerCss($block->style);
    }
    if ($block->script) {
        $this->registerJs($block->script);
    }
    $content=$block->getResultContent($this);
    $style=[];
    if ($block->background_color) {
        $style[]='background-color:'.$block->background_color.';';
    }
    if ($block->background_image) {
        $style[]='background-image:url("'.$block->background_image.'");';
    }
    echo Html::tag(
            'div',
            Html::tag('div',$content,['class'=>'promo-block-in']),
            [
                'class'=>'promo-block',
                'id'=>'promo-block-'.$block->block_id,
                'style'=>$style?implode(';', $style):null,
            ]
    );
    $n++;
}
?>
