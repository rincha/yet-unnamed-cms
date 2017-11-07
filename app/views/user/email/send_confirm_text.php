<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $authentication app\models\UserAuthentication */
?>
Была запрошена активация аккаунта на сайте <?= Html::a(Url::base(true), Url::base(true)) ?>.

Для завершения активации перейдите по ссылке: <?=
		Url::to(['/user/activate','uid'=>$authentication->uid,'type'=>$authentication->type, 'code'=>$authentication->verification],true)
?>

Либо можете ввести указанный ниже код на странице активации (<?= 
	Url::to(['/user/activate','uid'=>$authentication->uid,'type'=>$authentication->type],true) 
	?>).

Код активации: <?= Html::encode($authentication->verification); ?>

Если Вы не регистрировались на сайте <?= Url::base(true) ?>, просто проигнорируйте данное сообщение.