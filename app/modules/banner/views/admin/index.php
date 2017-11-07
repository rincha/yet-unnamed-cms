<?php

use yii\helpers\Html;
use app\common\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Баннеры';
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=>'type_id',
                'value'=>'typeText',
            ],
            'name',
            'start_at:date',
            'end_at:date',
            [
                'attribute'=>'status',
                'value'=>'statusText',
            ],

            ['class' => 'app\common\grid\ActionColumn'],
        ],
    ]); ?>
</div>
