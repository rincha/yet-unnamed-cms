<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\post\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('post', 'My posts');
$this->params['breadcrumbs'][] = Yii::t('post', 'My posts');
?>
<div class="post-index">

    <h1><?= Html::encode(Yii::t('post', 'My posts')) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'uid',
            'title',
            [
                'attribute'=>'status',
                'value'=>'statusText',
                'filter'=>$searchModel->getStatusList(),
            ],
            [
                'attribute'=>'_created_at',
                'value'=>function(\app\modules\post\models\Post $model) {
                    return $model->created_date.' '.$model->created_time;
                },
                'format'=>'datetime',
                'label'=>$searchModel->getAttributeLabel('created_time'),
                'filter'=> DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'_created_at',
                    'options'=>['class'=>'form-control'],
                ])
            ],
            //'created_date',
            // 'created_time',
            'updated_at:datetime',
            [
                'class' => 'app\common\grid\ActionColumn',
                'template'=>'{link} {view}',
                'buttons'=>[
                    'link'=>function($url, \app\modules\post\models\Post $model){
                        return Html::a('<i class="fa fa-link"></i>',$model->url,[
                            'title'=>Yii::t('post', 'Link'),
                            'class'=>'btn btn-sm btn-default',
                            'data-pjax'=>0,
                        ]);
                    },
                ],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
