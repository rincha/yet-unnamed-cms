<?php

use yii\helpers\Html;
use app\common\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\menu\models\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('menu', 'Menu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'menu_id',
            'key',
            'name',
            'type',
            [
                'class' => 'app\common\grid\ActionColumn',
                'template' => '{items} {update} {delete}',
                'buttons' => [
                    'items' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-list"></span>', ['items', 'id' => $model->menu_id], ['title' => Yii::t('menu', 'Menu items'), 'class'=>'btn btn-sm btn-default']);
                    }
                        ]
                    ],
                ],
            ]);
            ?>

</div>
