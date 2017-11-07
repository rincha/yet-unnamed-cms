<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\promo\models\PromoBlock */


$this->title = Yii::t('app', 'Update {modelClass}: ', ['modelClass'=>  Yii::t('promo', 'Promo-block')]).$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('promo', 'Promo pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->promo->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('promo', 'Promo-blocks'), 'url' => ['index', 'pid'=>$model->promo_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="promo-block-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('block-form', [
        'model' => $model,
    ]) ?>

</div>
