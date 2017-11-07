<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserAuthentication */
/* @var $verification string */

$this->title = Yii::t('app/user', 'Account activation');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-activate">

    <h1><?= Html::encode($this->title) ?></h1>

     <div class="user-activate-form">
        <?= Html::beginForm(['/user/activate'], 'get', []) ?>
        <?= Html::hiddenInput('type', $model->type) ?>
        <?= Html::hiddenInput('uid', $model->uid) ?>
        <div class="form-group">
            <?= Html::label($model->getAttributeLabel('verification'), 'verification', ['class'=>'']) ?>
            <?= Html::textInput('verification', $verification, ['class'=>'form-control']) ?>
        </div>


        <div class="form-group">
            <?= Html::submitButton(Yii::t('app/user', 'Activate'), ['class' => 'btn btn-success']) ?>
        </div>

        <?= Html::endForm(); ?>

    </div>

</div>
