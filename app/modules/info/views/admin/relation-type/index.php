<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('info', 'Relation types');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('info','Information materials'), 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="relation-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['relation-type-create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'type_id',
            'name',
            'title',
            [
                'class' => 'app\common\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'defaultButtonsActions'=>[
                    'view'=>'relation-type-view',
                    'update'=>'relation-type-update',
                    'delete'=>'relation-type-delete',
                ],
            ],

        ],
    ]); ?>

</div>
