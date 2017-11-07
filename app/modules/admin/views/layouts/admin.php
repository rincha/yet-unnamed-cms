<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\modules\admin\assets\AdminAsset;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="lyt-wrap">
    <header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->params['siteName'],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
        'innerContainerOptions'=>['class'=>'container-fluid'],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => Yii::t('app', 'Home'), 'url' => Yii::$app->homeUrl],
            [
                'label' => Yii::t('app', 'Logout ({username})',['username'=>Yii::$app->user->identity->username]),
                'url' => ['/user/logout','from_all'=>1],
                'linkOptions' => ['data-method' => 'post']
            ],
        ],
    ]);
    NavBar::end();
    ?>
    </header>
    <div class="lyt-content">
        <div class="lyt-content-row">
            <div class="lyt-content-col leftbar">
                <?php
                $items=[
                        ['label' => yii\bootstrap\Html::icon('menu-right').Html::tag('span',yii\bootstrap\Html::icon('menu-left').' '.Yii::t('app', 'Collapse'),['class'=>'text']), 'encode'=>false, 'url' => '#','options'=>['id'=>'adm-menu-maximize','class'=>'hidden-md hidden-lg adm-menu-minimized']],
                        ['label' => yii\bootstrap\Html::icon('user').Html::tag('span',' '.Yii::t('app/user', 'Users'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/user/index'], 'options'=>['title'=>Yii::t('app/user', 'Users')]],
                        ['label' => yii\bootstrap\Html::icon('lock').Html::tag('span',' '.Yii::t('rbac', 'Rights'),['class'=>'text']), 'encode'=>false, 'url' => ['/rbac/admin/index'], 'options'=>['title'=>Yii::t('rbac', 'Rights')]],
                        ['label' => yii\bootstrap\Html::icon('cog').Html::tag('span',' '.Yii::t('app', 'Settings'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/settings/index'], 'options'=>['title'=>Yii::t('app', 'Settings')]],
                        ['label' => yii\bootstrap\Html::icon('folder-open').Html::tag('span',' '.Yii::t('files', 'Files'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/files/index'], 'options'=>['title'=>Yii::t('files', 'Files')]],
                        ['label' => yii\bootstrap\Html::icon('th').Html::tag('span',' '.Yii::t('app/widgets', 'Widgets'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/widget/index'], 'options'=>['title'=>Yii::t('app/widgets', 'Widgets')]],
                ];
                foreach (Yii::$app->getModule('admin')->additionalControllers as $controllerClass) {
                    if (method_exists($controllerClass, 'apiAdmin')) {
                        $items=  array_merge($items, $controllerClass::apiAdmin()['menu']);
                    }
                }
                ?>
                <?=

                Nav::widget([
                    'options' => ['class' => '','id'=>'adm-menu'],
                    'items' => $items,
                ]);
                ?>
            </div>
            <div class="lyt-content-col content">
                <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= \app\common\widgets\FlashMessages\FlashMessages::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>

    <footer>
    <div class="container-fluid">
        <p class="pull-left">&copy; <?= Yii::$app->params['siteName'] ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
    </footer>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
