<?php

use yii\helpers\Html;
use app\common\helpers\ImageHelper;
use app\assets\BxSlider\BxSliderAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\banner\models\Banner */
/* @var $items app\modules\banner\models\BannerItem */
/* @var $id string */
/* @var $widget app\models\Widget */

?>
<div class="wgt-box wgt-banner wgt-banner-<?= $model->type_id ?>" id="<?= $id ?>">
    <?php if ($widget->title) { ?>
    <div class="title">
        <?= Html::encode($widget->title) ?>
    </div>
    <?php } ?>
    <div class="list slider" data-id="<?= $model->banner_id ?>" data-c="<?= count($items) ?>">
        <ul>
        <?php
        foreach ($items as $item) {
            $image= Html::img(ImageHelper::getThumbnail($item->image, $widget->getOptionsValue('thumbnail'), true),['class'=>'img-responsive']);
            if ($item->link) {
                $image_item=Html::a($image,$item->link);
            }
            else {
                $image_item=$image;
            }
            echo Html::tag('li',$image_item);
        }
        ?>
        </ul>
    </div>
</div>
<?php
BxSliderAsset::register($this);
$this->registerJs('$("#'.$id.' .list > ul").bxSlider({pager:false,autoHover:true,auto:true});');