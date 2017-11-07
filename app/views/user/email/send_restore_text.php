<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $authentication app\models\UserAuthentication */
/* @var $restore app\models\UserRestore */
?>
<?= Yii::t('app/user','Received a request to restore access for:') ?> <?= Url::base(true) ?>.

<?= Yii::t('app/user','To set a new password, please go to:') ?> 
<?= Url::to(['/user/restore','uid'=>$authentication->uid, 'type'=>$authentication->type,'code'=>$restore->reset_token],true) ?>

<?= Yii::t('app/user','Reset code:') ?> <?= Html::encode($restore->reset_token); ?>
<?= Yii::t('app/user','If you did not request a password reset on {site}, just ignore this message.',['site'=>Url::base(true)]) ?>
</div>