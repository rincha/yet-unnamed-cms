<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $authentication app\models\UserAuthentication */
/* @var $restore app\models\UserRestore */
?>
<div style="width:500px; margin:0 auto;">
	<p><?= Yii::t('app/user','Received a request to restore access for:') ?> <?= Html::a(Url::base(true), Url::base(true)) ?>.</p>
	<p><?= Yii::t('app/user','To set a new password, please go to:') ?> <?=
		Html::a(
				Url::to(['/user/restore','uid'=>$authentication->uid, 'type'=>$authentication->type,'code'=>$restore->reset_token],true), 
				Url::to(['/user/restore','uid'=>$authentication->uid, 'type'=>$authentication->type,'code'=>$restore->reset_token],true)
			)
		?></p>
	<p><?= Yii::t('app/user','Reset code:') ?></p>
	<div style="font-size: 20px; padding: 20px; background: #E0E0E0"><?= Html::encode($restore->reset_token); ?></div>
	<p><?= Yii::t('app/user','If you did not request a password reset on {site}, just ignore this message.',['site'=>Html::a(Url::base(true), Url::base(true))]) ?></p>
</div>