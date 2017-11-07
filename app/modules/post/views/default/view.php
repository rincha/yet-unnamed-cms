<?php

use yii\helpers\Html;
use app\common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Post */
/* @var $comment app\modules\post\models\Comment */
/* @var $commentsDp yii\data\ActiveDataProvider */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-default-view">

    <h1><?= Html::encode($model->h1?$model->h1:$model->title) ?></h1>

    <p>
        <strong><?= Html::encode($model->author?$model->author->username:null) ?></strong>,
        <span class="text-muted">
            <?= Yii::$app->formatter->asDate($model->created_date) ?>
        </span>
    </p>

    <div class="form-group">
        <?= $model->content ?>
    </div>

    <?php
    $imagesList=$model->getImageUrlList();
    $items=[];
    if ($imagesList) { ?>
    <div class="panel panel-default">
        <div class="panel-body">
        <?php foreach ($imagesList as $index=>$image) { ?>
        <?=
            Html::a(
                Html::img(
                    ImageHelper::getThumbnail($image, '100e100',true,['v'=>strtotime($model->updated_at)]),
                    ['class'=>'img-thumbnail']
                ),
                $image,
                ['target'=>'_blank','class'=>'img-zoom']
            )
        ?>
        <?php } ?>
        </div>
    </div>
    <?php } ?>

    <h2><?= Yii::t('post', 'Comments') ?></h2>
    <?= $this->render('_comments', [
        'dataProvider' => $commentsDp,
        'inBranch'=>false,
        'parent'=>null,
        'post'=>$model,
        'newComment'=>$comment,
    ]) ?>

    <h2><?= Yii::t('post', 'Add a comment') ?></h2>
    <?= $this->render('_comment_form', [
        'model' => $comment,
    ]) ?>

</div>
<?php
