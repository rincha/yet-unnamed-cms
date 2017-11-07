<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\info\models\Type */

$this->title = Yii::t('info', 'Information types').': '.Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('info','Information materials'), 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('info','Information types'), 'url'=>['type-index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
