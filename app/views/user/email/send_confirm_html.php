<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $authentication app\models\UserAuthentication */
?>
<div style="width:500px; margin:0 auto;">
    <p>Была запрошена активация аккаунта на сайте <?= Html::a(Url::base(true), Url::base(true)) ?>.</p>
    <p>Для завершения активации перейдите по ссылке: <?=
        Html::a(
                Url::to([
                    '/user/activate', 
                    'uid' => $authentication->uid,
                    'type' => $authentication->type,
                    'code' => $authentication->verification
                ], true), 
                Url::to([
                    '/user/activate', 
                    'uid' => $authentication->uid,
                    'type' => $authentication->type, 
                    'code' => $authentication->verification
                ], true)
        )
        ?></p>
    <p>Либо можете ввести указанный ниже код на <?=
        Html::a('странице активации', Url::to(['/user/activate', 'uid' => $authentication->uid, 'type' => $authentication->type], true))
        ?>.</p>
    <div style="font-size: 20px; padding: 20px; background: #E0E0E0"><?= Html::encode($authentication->verification); ?></div>
    <p>Если Вы не регистрировались на сайте <?= Html::a(Url::base(true), Url::base(true)) ?>, просто проигнорируйте данное сообщение.</p>
</div>