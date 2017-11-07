<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\post\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('post', 'Posts');
$this->params['breadcrumbs'][] = Yii::t('post', 'Posts');
?>
<div class="post-default-index">

    <h1><?= Html::encode(Yii::t('post', 'Posts')) ?></h1>


    <?php if ($searchModel->author || $searchModel->_date_start || $searchModel->_date_end) { ?>
    <p>
        <i class="fa fa-filter"></i> <?= Yii::t('post', 'Filters:') ?>
        <?php if ($searchModel->author) { ?>
        <?= Html::a(
                Yii::t('post', 'Author:').' '.Html::encode($searchModel->author->username).' <i class="fa fa-times"></i>',
                Url::to(['index','PostSearch[_date_start]'=>$searchModel->_date_start, 'PostSearch[_date_end]'=>$searchModel->_date_end]),
                ['class'=>'btn btn-default active']
        ) ?>
        <?php } ?>
        <?php if ($searchModel->_date_start && $searchModel->_date_end) { ?>
        <?= Html::a(
                Yii::t('post', 'Date:').' '.Html::encode($searchModel->_date_start.' - '.$searchModel->_date_end).' <i class="fa fa-times"></i>',
                Url::to(['index','PostSearch[author_id]'=>$searchModel->author_id]),
                ['class'=>'btn btn-default active']
        ) ?>
        <?php } ?>
    </p>
    <?php } ?>

    <?php if ($searchModel->hasErrors()) { ?>
    <div class="alert alert-danger"><?= Html::errorSummary($searchModel) ?></div>
    <?php } ?>

    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_post',
    ]);
    ?>

</div>
