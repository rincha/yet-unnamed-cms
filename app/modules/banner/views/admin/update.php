<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\banner\models\Banner */

$this->title = 'Изменить баннер: ' . $model->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>'Баннеры', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->banner_id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="banner-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
