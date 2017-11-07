<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('promo', 'Promo pages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['/admin/default/index']];
$this->params['breadcrumbs'][] = $this->title;

$temp=new \app\modules\promo\models\Promo;
?>
<div class="promo-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            'uid',
            'meta_title',
            [
                'attribute'=>'status',
                'value'=>function($model){return $model->statusName;},
                'filter'=>$temp->statusList(),
            ],
            'created_at',
            [
                'class' => 'yii\grid\ActionColumn', 'template'=>'{list} {update} {delete}',
                'buttons'=>[
                    'list' => function ($url, $model, $key) {
                        return Html::a(\yii\bootstrap\Html::icon('list'), ['block-index','pid'=>$model->promo_id], ['title'=>  Yii::t('promo', 'Block list')]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
