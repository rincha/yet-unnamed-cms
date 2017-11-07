<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\info\models\Info */

$this->title = Yii::t('info', 'Information materials').': '.$model->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('info', 'Information materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name
?>
<div class="info-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->info_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('info', 'Relations'), ['relations', 'id' => $model->info_id], ['class' => 'btn btn-info']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->info_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('info', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'info_id',
            'type_id',
            'uid',
            'name',
            'h1',
            'meta_title',
            'meta_description',
            'keywords',
            'images:ntext',
            'params:ntext',
            'date',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2><?= $model->getAttributeLabel('content') ?></h2>
        </div>
        <div class="panel-body">
            <?= $model->content ?>
        </div>
    </div>
</div>
