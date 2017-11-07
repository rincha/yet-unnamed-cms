<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $authentication app\models\UserAuthentication */
/* @var $restore app\models\UserRestore */
echo Yii::t('app/user','Reset code:').' '.Html::encode($restore->reset_token).' '.Url::base(true);