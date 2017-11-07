<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Widget */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app/widgets', 'Widget'),
]) . $model->title;

$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/widgets', 'Widgets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->widget_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="widget-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
