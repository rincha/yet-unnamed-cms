<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $url string */

$this->title = Yii::$app->params['siteName'];
?>
<div class="site-away text-center">
    <h1>External link warning</h1>
    <p class="lead">You are about to visit an external link.</p>
    <div class="well">Link: <?=  Html::a(Html::encode($url),$url,['rel'=>'nofollow']) ?></div>
    <p>In an effort to stop phishing, we are warning you:<br>
        <strong>DO NOT ENTER YOUR &quot;<?= Yii::$app->params['siteName'] ?>&quot; PASSWORD</strong> on this new website
    </p>
    <p>
        <?= Html::a(Yii::t('app', 'Cancel'), Url::home(), ['class'=>'btn btn-default'])?>
        <?= Html::a(Yii::t('app', 'Continue'), $url, ['class'=>'btn btn-default', 'rel'=>'nofollow'])?>
    </p>
</div>