<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Nav;
use app\modules\info\models\Type;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\modules\info\models\InfoSearch */

$this->title = Yii::t('info', 'Information materials');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="info-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('info', 'Information types'), ['type-index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('info', 'Relation types'), ['relation-type-index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            [
                'attribute'=>'type_id',
                'value'=>function($model){return $model->type?$model->type->name:null;},
                'filter'=>  yii\helpers\ArrayHelper::map(Type::find()->all(), 'type_id', 'name'),
            ],
            'uid',
            // 'meta_title',
            // 'meta_description',
            // 'keywords',
            // 'content:ntext',
            // 'images:ntext',
            // 'params:ntext',
            // 'date',
            'created_at',
            'updated_at',

            [
                'class' => 'app\common\grid\ActionColumn',
                'template' => '{relations} {view} {update} {delete}',
                'contentOptions'=>['style'=>'min-width:140px;'],
                'buttonSize'=>'',
                'buttons'=>[
                    'relations'=>function ($url, $model, $key) {
                            return;
                            $options = [
                                'title' => Yii::t('info', 'Relations'),
                                'aria-label' => Yii::t('info', 'Relations'),
                            ];
                            return Html::a('<span class="glyphicon glyphicon-resize-horizontal"></span>', ['relations','id'=>$model->info_id], $options);
                        },
                ]
            ],
        ],
    ]); ?>

</div>
