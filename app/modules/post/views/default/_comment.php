<?php

use yii\helpers\Html;
use yii\bootstrap\ButtonDropdown;
use app\modules\post\models\Comment;
use app\modules\post\models\CommentSearch;

/* @var $this yii\web\View */
/* @var $post app\modules\post\models\Post */
/* @var $model app\modules\post\models\Comment */
/* @var $parent app\modules\post\models\Comment|null */
/* @var $newComment app\modules\post\models\Comment */
$isPublished=in_array($model->status, Yii::$app->getModule('post')->commentDisplayStatus);


?>
<div class="comment" id="comment-<?= $model->comment_id ?>">
    <a name="comment-<?= $model->comment_id ?>"></a>
    <div class="pull-right btn-group">
        <?php if ($newComment->isAttributeActive('parent_id')) { ?>
        <?= Html::a('<i class="fa fa-reply"></i> '.Yii::t('post', 'Reply'),'#post-comment-form',['class'=>'btn btn-default btn-sm post-comment-reply-btn','data-id'=>$model->comment_id])?>
        <?php } ?>
        <?php if ($post->isCanUpdate()) { ?>
        <?= ButtonDropdown::widget([
            'label'=>'<i class="fa fa-cog"></i>',
            'encodeLabel'=>false,
            'dropdown'=>[
                'items'=>[
                    [
                        'label'=>Yii::t('post', 'Approve'),
                        'url'=>['/post/author/comment-status','id'=>$model->comment_id,'status'=>Comment::STATUS_APPROVED],
                        'visible'=>$model->status!=Comment::STATUS_APPROVED,
                        'linkOptions'=>[
                            'data-method'=>'post',
                            'data-pjax'=>0,
                        ],
                    ],
                    [
                        'label'=>Yii::t('post', 'Arhive'),
                        'url'=>['/post/author/comment-status','id'=>$model->comment_id,'status'=>Comment::STATUS_ARCHIVED],
                        'visible'=>$model->status!=Comment::STATUS_ARCHIVED,
                        'linkOptions'=>[
                            'data-method'=>'post',
                            'data-pjax'=>0,
                        ],
                    ],
                ],
                'options'=>['class'=>'dropdown-menu-right post-comment-actions'],
            ],
            'options'=>['class'=>'btn btn-sm btn-default'],
        ]) ?>
        <?php } ?>
    </div>
    <div class="data<?= $isPublished?'':' bg-warning' ?>">
        <p>
            <?php if ($model->author) { ?>
            <strong><?= Html::encode($model->author->username) ?></strong>,
            <?php } else { ?>
            <strong><?= Html::encode($model->author_nickname) ?></strong>,
            <?php } ?>
            <span class="text-muted">
            <?= Yii::$app->formatter->asDate($model->created_date) ?>
            <?= Yii::$app->formatter->asTime($model->created_time) ?>
            </span>
        </p>
        <?php
        if ($parent && $parent->comment_id!=$model->parent_id) {
        ?>
        <p>
            <em><?= Yii::t('post', 'Reply to message') ?>:
            <?= $this->render('_comment_reply',['model'=>$model->parent]) ?>
            </em>
        </p>
        <?php
        }
        ?>
        <div class="cont">
            <?= $model->content ?>
        </div>
    </div>
    <?php
    if ($model->hasBranch) {
        $commentSearch=new CommentSearch();
        $commentSearch->post_id=$post->post_id;
        if (!$post->isCanUpdate()) {
            $commentSearch->status=Yii::$app->getModule('post')->commentDisplayStatus;
        }
        $commentSearch->branch_id=$model->comment_id;
        $commentsDp=$commentSearch->search([]);
        Comment::queryAddHasBranch($commentsDp->query);
        echo $this->render('_comments', [
            'dataProvider' => $commentsDp,
            'parent'=>$model,
            'post'=>$post,
            'newComment'=>$newComment,
        ]);
    }
    ?>
</div>
