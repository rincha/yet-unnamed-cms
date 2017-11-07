<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;

/* @var $this \app\common\web\View */
/* @var $content string */
?>
<?php $this->beginContent('@app/views/layouts/base.php'); ?>
<div class="thm-site">
<header class="thm-header">
    <div class="container-fluid">
        <div class="pull-right">
            <?php
            $items = [];
            if (Yii::$app->user->isGuest) {
                $items[] = ['label' => Yii::t('app/user', 'Login'), 'url' => ['/user/login']];
                $items[] = ['label' => Yii::t('app/user', 'Register'), 'url' => ['/user/register']];
            } else {
                if (isset(Yii::$app->user->identity->authentications['email'])) {
                    $username = Yii::$app->user->identity->authentications['email']->uid;
                } else {
                    $username = Yii::$app->user->identity->username;
                }
                $items_user = [];
                $items_user[] = ['label' => Yii::t('app/user', 'My account'), 'url' => ['/u/default/index']];
                if (Yii::$app->user->can('Admin.Default.Index')) {
                    $items_user[] = ['label' => Yii::t('app', 'Administration'), 'url' => ['/admin/default/index']];
                }
                $items[] = ['label' => \yii\helpers\StringHelper::truncate($username, 32), 'url' => ['/u/default/index'], 'items' => $items_user];
                $items[] = [
                    'label' => Yii::t('app/user', 'Logout'),
                    'url' => ['/user/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'nav nav-pills'],
                'items' => $items,
            ]);
            ?>
        </div>
        <?= Html::a(Yii::$app->params['siteName'], '/', ['class' => 'thm-logo']) ?>
    </div>
    <div>
        <?= $this->runWidgets('header') ?>
    </div>

</header>
<?php if ($this->hasWidgets('afterHeader')) { ?>
    <aside class="thm-bar-after-header">
        <?= $this->runWidgets('afterHeader') ?>
    </aside>
<?php } ?>
<div class="thm-content">
    <div class="thm-content-wrap">
        <div class="container-fluid">
            <?php if ($this->hasWidgets('top')) { ?>
                <aside class="thm-bar-top">
                    <?= $this->runWidgets('top') ?>
                </aside>
            <?php } ?>
            <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= \app\common\widgets\FlashMessages\FlashMessages::widget(); ?>
            <?php
            $content_class = 'col-xs-12';
            if ($this->hasWidgets('left')) {
                $content_class = 'col-xs-8';
            }
            ?>
            <div class="thm-main">
                <?php if ($this->hasWidgets('left')) { ?>
                    <aside class="thm-bar-left">
                        <?= $this->runWidgets('left') ?>
                    </aside>
                <?php } ?>
                <div class="thm-main-content">
                    <?= $content ?>
                </div>
            </div>
            <?php if ($this->hasWidgets('bottom')) { ?>
                <aside class="thm-bar-bottom">
                    <?= $this->runWidgets('bottom') ?>
                </aside>
            <?php } ?>
        </div>
    </div>
</div>
<?php if ($this->hasWidgets('beforeFooter')) { ?>
    <aside class="thm-bar-before-footer">
        <?= $this->runWidgets('beforeFooter') ?>
    </aside>
<?php } ?>
<footer class="thm-footer">
    <div class="container-fluid">
        <div class="pull-left">
            &copy; <?= Yii::$app->params['siteName'] ?> <?= date('Y') ?>
        </div>
        <?php if ($this->hasWidgets('footer')) { ?>
            <aside class="thm-bar-footer">
                <?= $this->runWidgets('footer') ?>
            </aside>
        <?php } ?>
    </div>
</footer>
</div>
<?php if ($this->hasWidgets('end')) { ?>
    <aside class="thm-bar-end">
        <?= $this->runWidgets('end') ?>
    </aside>
<?php } ?>
<?php $this->endContent(); ?>
