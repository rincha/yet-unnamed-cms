<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $authentication app\models\UserAuthentication */
?>Была запрошена активация аккаунта на сайте <?= Html::a(Url::base(true), Url::base(true)) ?>.
Код активации: <?= Html::encode($authentication->verification); ?>