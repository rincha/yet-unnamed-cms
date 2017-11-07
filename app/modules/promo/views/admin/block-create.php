<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\promo\models\PromoBlock */
/* @var $promo app\modules\promo\models\PromoBlock */

$this->title = Yii::t('promo', 'Create Promo Block');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('promo', 'Promo pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $promo->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('promo', 'Promo-blocks'), 'url' => ['index', 'pid'=>$promo->promo_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="promo-block-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('block-form', [
        'model' => $model,
    ]) ?>

</div>
