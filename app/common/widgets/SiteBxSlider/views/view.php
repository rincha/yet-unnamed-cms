<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $this->context app\common\widgets\SiteBxSlider\SiteBxSlider */
/* @var $model app\models\Widget */

$images=ArrayHelper::getValue($model->options, 'images', []);
$links=ArrayHelper::getValue($model->options, 'links', []);
$titles=ArrayHelper::getValue($model->options, 'titles', []);
$descriptions=ArrayHelper::getValue($model->options, 'descriptions', []);
?>
<div class="bx-slider" id="<?= $this->context->id ?>">
<?php
foreach ($images as $k=>$image) {
    if (!$image) {continue;}
    $text='';
    if ($titles[$k]) {
        $text=Html::tag(
                'span',
                Html::tag('span', Html::encode($titles[$k]), ['class'=>'title']).
                ($descriptions[$k]?Html::tag('span', Html::encode($descriptions[$k]), ['class'=>'desc']):''),
                ['class'=>'text']
        );
    }    
    
    ?>
    <?= 
        $links[$k]
        ?
        Html::a(
            Html::img($image,['alt'=>$titles[$k], 'class'=>'img-responsive']).$text,
            $links[$k],
            ['class'=>'item']
        )
        :
        Html::tag(
            'div',
            Html::img($image,['alt'=>$titles[$k], 'class'=>'img-responsive']).$text,
            ['class'=>'item']
        )
    ?>
    <?php
}

?>
</div>