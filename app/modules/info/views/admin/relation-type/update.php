<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\info\models\RelationType */

$this->title = Yii::t('app', 'Update {modelClass}:', [
    'modelClass' => Yii::t('info', 'Relation type'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('info','Information materials'), 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('info', 'Relation types'), 'url' => ['relation-type-index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->type_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="relation-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
