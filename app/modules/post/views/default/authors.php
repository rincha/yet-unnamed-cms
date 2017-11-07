<?php

use yii\helpers\Html;
use app\common\grid\GridView;
use app\models\User;
use app\modules\post\models\Post;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('post', 'Authors');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('post','Posts'), 'url'=>['/post/default/index']];
$this->params['breadcrumbs'][] = Yii::t('post', 'Authors');
?>
<div class="post-default-authors">

    <h1><?= Yii::t('post', 'Authors') ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label'=>Yii::t('post', 'Author'),
                'attribute' => 'username',
                'value' => function(User $model){
                    return Html::a(Html::encode($model->username),['/post/default/index','PostSearch[author_id]'=>$model->id]);
                },

                'format'=>'raw',
            ],
            'created_at:datetime',
            [
                'label' => Yii::t('post', 'Posts count'),
                'value' => function(User $model){
                    return Post::find()->where(['author_id'=>$model->id])->count();
                },
            ],
        ],
    ]);
    ?>

</div>
