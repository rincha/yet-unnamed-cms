<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\info\models\Info */

$this->title = Yii::t('info', 'Information materials').': '.Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('info', 'Information materials'), 'url' => ['index']];

$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
