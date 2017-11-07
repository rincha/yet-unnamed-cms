<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\forms\models\Form */

$this->title = Yii::t('forms','Forms').': '.$model->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms','Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;

?>
<div class="form-view">

    <h1><?= Yii::t('forms', 'Form') ?> [<?= Html::encode($model->name) ?>]</h1>

    <h2><?= Yii::t('forms', 'Fields') ?>:</h2>

    <p>
        <?= Html::a(Yii::t('app','Add'), ['field-create', 'id' => $model->form_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => new yii\data\ArrayDataProvider([
            'allModels' => $model->fields,
            'sort' => [
                'attributes' => [],
            ],
            'pagination' => [
                'pageSize' => 999,
            ],

        ]),
        'filterModel' => false,
        'columns' => [
            'title',
            'name',
            'typeName',
            'required',
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons'=>[
                    'update'=>function ($url, $model) {
                        return \yii\helpers\Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                ['field-update','id'=>$model->field_id],
                                ['title' => Yii::t('yii', 'Update')]
                        );
                    },
                    'delete'=>function ($url, $model) {
                        return \yii\helpers\Html::a(
                                '<span class="glyphicon glyphicon-trash"></span>',
                                ['field-delete','id'=>$model->field_id],
                                ['title' => Yii::t('yii', 'Delete')]
                        );
                    },
                ],
                'template'=>'{update} {delete}',
            ],
        ],
    ]); ?>

    <h2><?= Yii::t('forms', 'Form data') ?></h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'URL',
                'value'=>  yii\helpers\Url::to($model->url),
            ],
            'name',
            'type',
            'status',
            'emails',
            'phone',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->form_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->form_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

</div>
