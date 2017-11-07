<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\widgets\tinymce\Tinymce;
use yii\helpers\ArrayHelper;
use app\modules\news\models\NewsType;
use app\modules\files\widgets\FileSelect\FileSelectInput;

/* @var $this yii\web\View */
/* @var $model app\modules\news\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <?php
            $items=[''=>'']+ArrayHelper::map(NewsType::find()->all(), 'type_id', 'title');
            ?>
            <?= $form->field($model, 'type_id')->dropDownList($items) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(),[
                'options'=>['class'=>'form-control'],
            ]) ?>
        </div>
    </div>
    
    <?= $form->field($model, 'content')->widget(Tinymce::className(),[
        'config'=>  \app\common\helpers\AppHelper::getTinyMceConfig(),
        'options'=>['style'=>'height:400px;']
    ]) ?>

   <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#admin-news-form-additional">
                        <?= Yii::t('app', 'Extended properties')?> <i class="fa fa-angle-down"></i>
                    </a>
                </h4>
            </div>
            <div id="admin-news-form-additional" class="panel-collapse collapse<?= 
                    ($model->meta_description||$model->keywords||$model->images||$model->h1||$model->meta_title)?' in':''
                ?>">
                <div class="panel-body">   
                <div class="row">
                    <div class="col-sm-6"><?= $form->field($model, 'h1')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-sm-6"><?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?></div>
                </div>
                <?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'keywords')->textarea(['rows' => 3]) ?>    
                <?= $form->field($model, 'images')->widget(FileSelectInput::className(),[
                    'name'=>'News[images][]',
                    'value'=> is_array($model->images)?$model->images:explode("\n", $model->images),
                    'multiple'=>true,
                    'multipleCount'=>3
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
