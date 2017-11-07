<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\news\models\News */

$this->title = $model->meta_title?$model->meta_title:$model->name;
$this->registerMetaTag(['name'=>'description','content'=>$model->meta_description]);


$this->params['breadcrumbs'][] = ['label' => Yii::t('news', 'News'), 'url' => ['index']];
if ($model->type) {
    $this->params['breadcrumbs'][] = ['label' => $model->type->title, 'url' => ['index','type'=>$model->type->name]];
}
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="news-view">
    
    <?php if ($model->getImage()) { ?>
    <div class="news-view-images">
        <?= Html::a(Html::img(ImageHelper::getThumbnail($model->getImage(), '200e200')), $model->getImage(), ['alt'=>$model->name]) ?>
    </div>
    <?php } ?>
    
    <h1><?= Html::encode($model->name) ?></h1>
    
    <span class="news-view-date"><?= Yii::$app->formatter->asDate($model->date) ?></span>
    
    <?= $model->content ?>

</div>
