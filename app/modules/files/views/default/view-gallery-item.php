<?php

use yii\helpers\Html;
use app\common\helpers\AppHelper;
use app\common\helpers\ImageHelper;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\File */
?>
<div class="col-xs-6 col-sm-4 col-lg-3 file-item text-center form-group">
    <?=
    Html::a(
        Html::img($model->getTmb('300e300'),['class'=>'img-responsive img-thumbnail','alt'=>$model->name]),
        $model->url,
        ['class'=>'img-zoom file','target'=>'_blank']
    )
    ?>
</div>