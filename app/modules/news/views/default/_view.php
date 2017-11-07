<?php

use yii\helpers\Html;
use app\common\helpers\ImageHelper;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\news\models\News */

?>
<div class="news-index-item">
    
    <?php if ($model->getImage()) { ?>
        <?= Html::a(Html::img(ImageHelper::getThumbnail($model->getImage(), '100e100')), $model->url, ['alt'=>$model->name]) ?>
    <?php } ?>
    <h2><?= Html::a(Html::encode($model->name),$model->url) ?></h2>
    <span class="news-index-date"><?= Yii::$app->formatter->asDate($model->date) ?></span>
    <div class="desc">
        <?= $model->meta_description?$model->meta_description:StringHelper::truncate(strip_tags($model->content), 512) ?>
    </div>
</div>
