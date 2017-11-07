<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $authentication app\models\UserAuthentication */
/* @var $username string */
/* @var $password string */
?>
<div style="width:500px; margin:0 auto;">
    <p>Вы были зарегистрированы на сайте <?= Html::a(Url::base(true), Url::base(true)) ?>.</p>
    <p>Ваш логин: <?= $username ?></p>
    <p>Ваш пароль: <?= $password ?></p>
</div>