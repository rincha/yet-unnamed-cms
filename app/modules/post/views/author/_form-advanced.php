<?php

/* @var $model app\modules\post\models\Post */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="post-form-advanced">
    <?= $form->field($model, 'h1')->textInput(['maxlength' => true]) ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'description')->textarea(['rows'=>4]) ?>
        </div>
    </div>
    <?php if ($model->isAttributeActive('status')) { ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>
    <?php } ?>
    <?php if ($model->isAttributeActive('author_id')) { ?>
    <?= $form->field($model, 'author_id')->textInput() ?>
    <?php } ?>
</div>
