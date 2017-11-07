<?php
use yii\bootstrap\Nav;
/* @var $this yii\web\View */

$this->title = Yii::t('post','Posts');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-admin-index">
    <h1><?= Yii::t('post','Posts') ?></h1>
    <?=
    Nav::widget([
        'items'=>[
            ['label'=>Yii::t('post', 'Settings'),'url'=>['settings']],
            ['label'=>Yii::t('post', 'Users'),'url'=>['users']],
        ],
    ])
    ?>
</div>
