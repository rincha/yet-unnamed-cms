<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $model app\models\UserAuthentication */
/* @var $form ActiveForm */
/* @var $disabled boolean */
?>
<div class="user-activate-type">
    <?php
    if ($model->errors) {
        ?><div class="alert alert-danger"><?= Html::errorSummary($model); ?></div><?php
    }
    ?>
    <?php
    if (!$model->typeModel->protocol) {
    ?>

    <?= $form->field($model, 'uid',['template'=>"{input}\n{hint}\n{error}"])->textInput(['maxlength' => 64, 'disabled'=>$disabled]) ?>

    <?= $form->field($model, 'type',['template'=>"{input}\n{hint}\n{error}"])->hiddenInput(['disabled'=>$disabled]) ?>

    <?php
    } 
    ?>
</div>
