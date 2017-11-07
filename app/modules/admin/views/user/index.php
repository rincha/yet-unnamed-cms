<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/user', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'username',
                'value' => 'uid',
            ],
            [
                'attribute' => 'status',
                'value' => 'statusName',
                'filter' => User::getStatusesList(),
            ],
            [
                'attribute' => '_authentication',
                //'label' => 'Аккаунты',
                'value' => function($model) {
                    $result = [];
                    foreach ($model->authentications as $a) {
                        $result[] = $a->type . ':' . $a->uid;
                    }
                    return implode("\n", $result);
                },
                'format' => 'ntext',
            ],
            'created_at',
            'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
