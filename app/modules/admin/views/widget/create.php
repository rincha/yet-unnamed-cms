<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Widget */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('app/widgets', 'Widget')]);
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/widgets', 'Widgets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="widget-create">

    <h1><?= Html::encode($this->title) ?>: <?= $model->typeName ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
