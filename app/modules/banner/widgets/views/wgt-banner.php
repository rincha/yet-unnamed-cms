<?php

use yii\helpers\Html;
use app\common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\banner\models\Banner */
/* @var $id string */
/* @var $widget app\models\Widget */

?>
<div class="wgt-box wgt-banner wgt-banner-<?= $model->type_id ?>" id="<?= $id ?>">
    <?php if ($widget->title) { ?>
    <div class="title">
        <?= Html::encode($widget->title) ?>
    </div>
    <?php } ?>
    <div class="list">
        <ul>
        <?php
        foreach ($model->items as $item) {
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