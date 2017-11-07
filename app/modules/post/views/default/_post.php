<?php

use yii\helpers\Html;
use app\common\helpers\ImageHelper;
use yii\helpers\StringHelper;
use app\common\helpers\AppHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Post */

?>
<div class="post">

    <div class="row">
        <?php
        if ($model->getImagePathList()) {
            $col='col-xs-8 col-md-9 col-lg-10';
            ?>
        <div class="col-xs-4 col-md-3 col-lg-2">
            <?= Html::a(
                Html::img(
                    ImageHelper::getThumbnail($model->getImageUrl(), '200e200',true, ['v'=>strtotime($model->updated_at)]),
                    ['class'=>'img-thumbnail img-responsive']
                ),
                $model->url
            ) ?>
        </div>
            <?php
        }
        else {
            $col='col-xs-12';
        }
        ?>
        <div class="<?= $col ?>">
            <h2><?= Html::a(Html::encode($model->title),$model->url) ?></h2>
            <p>
                <strong><?= Html::encode($model->author?$model->author->username:null) ?></strong>,
                <span class="text-muted">
                    <?= Yii::$app->formatter->asDate($model->created_date) ?>
                </span>
            </p>
            <p class="desc">
            <?php if ($model->description) { ?>
            <?= Yii::$app->formatter->asNtext($model->description) ?>
            <?php } else { ?>
            <?= AppHelper::stringWrap(StringHelper::truncate(strip_tags($model->content), 512,'...',null,true))?>
            <?php } ?>
            </p>
            <p>
                <?= Html::a(Yii::t('post', 'Read more'),$model->url,['class'=>'btn btn-default']) ?>
            </p>
        </div>
    </div>
    <hr>
</div>
