<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\news\models\News */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('news', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->news_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->news_id], [
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
            'news_id',
            'name',
            'date:date',
            'uid',
            'type.title',           
            'h1',
            'meta_title',
            'meta_description',
            'keywords',           
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Yii::t('news', 'Content') ?>
        </div>
        <div class="panel-body">
            <?= $model->content ?>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Yii::t('news', 'Images') ?>
        </div>
        <div class="panel-body">
            <?php
                foreach ($model->images as $image) {
                    if ($image) {
                        echo Html::a(Html::img(ImageHelper::getThumbnail($image, '100e100')),$image, ['target'=>'_blank']);
                    }
                }
            ?>
        </div>
    </div>
    

</div>
