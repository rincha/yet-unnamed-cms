<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\helpers\ImageHelper;
use app\common\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\banner\models\Banner */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>'Баннеры', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->banner_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->banner_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'banner_id',
            'typeText',
            'name',
            'title',
            'text:ntext',
            'data:ntext',
            'start_at',
            'end_at',
            'statusText',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <p>
        <?= Html::a('Добавить изображение', ['item-create','id'=>$model->banner_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=>'image',
                'value'=>function($m){
                    return Html::a(
                            Html::img(ImageHelper::getThumbnail($m->image, '50e50', true)),
                            $m->image,
                            ['class'=>'img-zoom']
                    );
                },
                'format'=>'raw'
            ],
            'title',
            'link',
            'start_at:date',
            'end_at:date',
            [
                'attribute'=>'status',
                'value'=>'statusText',
            ],

            [
                'class' => 'app\common\grid\ActionColumn',
                'template'=>'{update}{delete}',
                'defaultButtonsActions'=>[
                    'delete'=>'item-delete',
                    'update'=>'item-update',
                ]

            ],
        ],
    ]); ?>

</div>
