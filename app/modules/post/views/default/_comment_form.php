<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\widgets\tinymce\Tinymce;
use app\common\helpers\AppHelper;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-comment-form">
    <a name="post-comment-form"></a>
    <?php $form = ActiveForm::begin([
        'enableClientValidation'=>true,
    ]); ?>

    <?php if ($model->hasErrors()) { ?>
    <div class="alert alert-danger">
        <?= Html::errorSummary($model) ?>
    </div>
    <?php } ?>

    <div id="post-form-answer" class="panel panel-default" style="display: none;">
        <div class="panel-heading">
            <?= Yii::t('post', 'Reply to message') ?>
        </div>
        <div class="panel-body">
            loading... fail
        </div>
        <div class="panel-footer">
            <button class="btn btn-danger" type="button"><?= Yii::t('app', 'Cancel') ?></button>
        </div>
    </div>

    <div class="row">
        <?php if ($model->isAttributeActive('author_nickname')) { ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'author_nickname')->textInput(['maxlength' => true]) ?>
        </div>
        <?php } ?>
        <?php if ($model->isAttributeActive('author_email')) { ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'author_email')->textInput(['maxlength' => true]) ?>
        </div>
        <?php } ?>
    </div>

    <?= $form->field($model, 'parent_id',['template'=>'{input}'])->hiddenInput() ?>

    <?=
    $form->field($model, 'content',['enableClientValidation'=>false])->widget(Tinymce::className(), [
        'config' => AppHelper::getTinyMceConfig('simple_links'),
        'options' => ['style' => 'height:100px;']
    ])
    ?>

    <?php if ($model->isAttributeActive('verifyCode')) { ?>
    <div class="form-group">
        <?= $form->field($model, 'verifyCode', ['enableClientValidation' => false])->widget(Captcha::className(), ['captchaAction' => '/site/captcha']) ?>
    </div>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js=<<<JS
    $('#post-form-answer .btn-danger').on("click",function(){
        $(this).parents("div.panel").slideUp();
        $("#comment-parent_id").val("");
    });
JS;
$this->registerJs($js);
