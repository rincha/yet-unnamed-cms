<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\helpers\ImageHelper;
use app\modules\post\models\Post;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Post */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'My posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('post', 'Link'), $model->url, ['class' => 'btn btn-default']) ?>
        <?php if ($model->status==Post::STATUS_DRAFT) { ?>
        <?= Html::a(Yii::t('post', 'Publish'), ['publish', 'id' => $model->post_id, 'publish'=>1], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure want to publish this post?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php } elseif ($model->status==Post::STATUS_NEW) { ?>
        <?= Html::a(Yii::t('post', 'Put in drafts'), ['publish', 'id' => $model->post_id, 'publish'=>0], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure want to put in drafts this post?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php } ?>
        <?= Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->post_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->post_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'post_id',
            'uid',
            'author.username',
            'title',
            'h1',
            'description:ntext',
            'keywords',
            'statusText',
            'created_date:date',
            'created_time:time',
            'updated_at:datetime',
        ],
    ]) ?>

    <?php
    $imagesList=$model->getImageUrlList();
    $items=[];
    if ($imagesList) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Html::encode($model->getAttributeLabel('images')) ?>
        </div>
        <div class="panel-body">
        <?php foreach ($imagesList as $index=>$image) { ?>
        <?=
            Html::a(
                Html::img(
                    ImageHelper::getThumbnail($image, '100e100',true,['v'=>strtotime($model->updated_at)]),
                    ['class'=>'img-thumbnail img-zoom']
                ),
                $image,
                ['target'=>'_blank']
            )
        ?>
        <?php } ?>
        </div>
    </div>
    <?php } ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Html::encode($model->getAttributeLabel('content')) ?>
        </div>
        <div class="panel-body">
            <?= $model->content ?>
        </div>
    </div>

</div>
