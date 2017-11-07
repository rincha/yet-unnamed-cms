<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\promo\models\Promo */

$this->title = Yii::t('app', 'Update {modelClass}: ', ['modelClass'=>  Yii::t('promo', 'Promo page')]).$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('promo', 'Promo pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
