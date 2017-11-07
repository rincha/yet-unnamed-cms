<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\widgets\tinymce\Tinymce;
use app\common\helpers\AppHelper;
use app\common\widgets\FileInput\FileInput;
use app\common\helpers\ImageHelper;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation'=>true,
        'options'=>['enctype'=>'multipart/form-data'],
    ]); ?>

    <?php if ($model->hasErrors()) { ?>
    <div class="alert alert-danger">
        <?= Html::errorSummary($model) ?>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?php
            $imagesList=$model->getImageUrlList();
            $items=[];
            if ($imagesList) { ?>
            <div class="well">
                <?php foreach ($imagesList as $index=>$image) { ?>
                <?php
                $items[$index]=Html::img(ImageHelper::getThumbnail($image, '100e100').'?ver='.strtotime($model->updated_at),['class'=>'img-thumbnail'])
                ?>
                <?php } ?>
                <?=
                $form->field($model, 'images_delete[]')->checkboxList($items,['encode'=>false, 'separator'=>'&nbsp; &nbsp; &nbsp; &nbsp;']);
                ?>
            </div>
            <?php } ?>
            <?= $form->field($model, 'images_add[]')->widget(FileInput::className(),['options'=>['multiple'=>'multiple']]) ?>
        </div>
    </div>

    <?=
    $form->field($model, 'content',['enableClientValidation'=>false])->widget(Tinymce::className(), [
        'config' => AppHelper::getTinyMceConfig('light_links'),
        'options' => ['style' => 'height:300px;']
    ])
    ?>

    <?=
    Collapse::widget([
        'items'=>[
            [
                'label'=>Yii::t('post', 'Advanced properties'),
                'content'=>$this->render('_form-advanced',['model'=>$model,'form'=>$form]),
                'contentOptions' => ['class' => $model->hasErrors()?'in':null]
            ],
        ],
    ])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
