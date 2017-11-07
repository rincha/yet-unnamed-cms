<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $banner app\modules\banner\models\Banner */
/* @var $model app\modules\banner\models\BannerItem */

$this->title = 'Добавить изображение к баннеру: '.$banner->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>'Баннеры', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => $banner->name, 'url' => ['view', 'id' => $banner->banner_id]];
$this->params['breadcrumbs'][] = 'Добавить изображение';
?>
<div class="banner-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('item-form', [
        'model' => $model,
    ]) ?>

</div>
