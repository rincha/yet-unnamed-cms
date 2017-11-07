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

<div class="wrap">
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

    <div class="container-fluid">
        <div class="row">
            <div class="adm-container-table">
            <div class="adm-container-row">
            <div class="col-xs-1 col-sm-1 col-md-3 adm-col adm-leftbar">
                <?php
                $items=[
                        ['label' => yii\bootstrap\Html::icon('menu-right').Html::tag('span',yii\bootstrap\Html::icon('menu-left').' '.Yii::t('app', 'Collapse'),['class'=>'text']), 'encode'=>false, 'url' => '#','options'=>['id'=>'adm-menu-maximize','class'=>'hidden-md hidden-lg adm-menu-minimized']],
                        ['label' => yii\bootstrap\Html::icon('folder-open').Html::tag('span',' '.Yii::t('app', 'Files'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/files/index']],
                        ['label' => yii\bootstrap\Html::icon('user').Html::tag('span',' '.Yii::t('app/user', 'Users'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/user/index']],
                        ['label' => yii\bootstrap\Html::icon('lock').Html::tag('span',' '.Yii::t('app/user', 'Rights'),['class'=>'text']), 'encode'=>false, 'url' => ['/rbac/admin/index']],
                        ['label' => yii\bootstrap\Html::icon('cog').Html::tag('span',' '.Yii::t('app', 'Settings'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/settings/index']],
                        ['label' => yii\bootstrap\Html::icon('file').Html::tag('span',' '.Yii::t('info', 'Information materials'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/info/index']],
                        ['label' => yii\bootstrap\Html::icon('menu-hamburger').Html::tag('span',' '.Yii::t('menu', 'Menu'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/menu/index']],
                        ['label' => yii\bootstrap\Html::icon('list-alt').Html::tag('span',' '.Yii::t('forms', 'Forms'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/forms/index']],
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
            <div class="col-xs-11 col-sm-11 col-md-9 adm-col">
                <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= $content ?>
            </div>        
            </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container-fluid">
        <p class="pull-left">&copy; <?= Yii::$app->params['siteName'] ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
