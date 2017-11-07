<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\info\models\Type;
use yii\helpers\ArrayHelper;
use app\common\widgets\tinymce\Tinymce;
use app\modules\files\widgets\FileSelect\FileSelectInput;


/* @var $this yii\web\View */
/* @var $model app\modules\info\models\Info */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="info-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <?php
    $conf=\app\common\helpers\AppHelper::getTinyMceConfig();
    //linkListCtrl
    $conf['file_browser_callback_types']='file image media';
    $conf['file_browser_callback']=new \yii\web\JsExpression('function(field_name, url, type, win){'
    . 'top.tinymce.activeEditor.execCommand("mceRfiles",true,function(url){'
    . 'win.document.getElementById(field_name).value=url;'
    . 'top.tinymce.activeEditor.windowManager.close();'
    . '});'
    . '}');
    $conf['link_list']= \yii\helpers\Url::to(['/admin/default/last-info-links']);
    $conf['link_extended_buttons']= [
        [
        'name'=> 'select',
        'type'=> 'button',
        'text'=> 'Выбрать материал',
        'label'=> '',
        'style'=> 'border:1px solid #F00;',
        'onclick'=> new \yii\web\JsExpression('function(e){'
                . 'var input=$(e.target).parents(".mce-container-body:first").find(".mce-container:first input");'
                . '$(input).attr("id","tmp-tinymce-link-select-btn");'
                . '$("#" + tinymce.activeEditor.windowManager.windows[0]._id).css("z-index", 99);'
                . '$("#mce-modal-block").css("z-index", 98);'
                . '$("#tmp-link-select-wgt").modal("show");'
                . '$("#tmp-link-select-wgt").find(".btn-primary").click(function(){'
                . 'var data=JSON.parse($(this).attr("data-url"));'
                . '$("#tmp-tinymce-link-select-btn").val(data.url);'
                . '});'
                . '}'),
        ],
    ];
    $conf['plugins'][array_search('link', $conf['plugins'])]='rlink';
    ?>

    <?= \app\modules\admin\widgets\LinksSelectWidget::widget(['id'=>'tmp-link-select-wgt']) ?>

    <?= $form->field($model, 'content')->widget(Tinymce::className(),[
        'config'=>  $conf,
        'options'=>['style'=>'height:400px;']
    ]) ?>

    <?= $form->field($model, 'safe')->checkbox() ?>

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#admin-info-form-additional">
                        <?= Yii::t('app', 'Extended properties')?> <i class="fa fa-angle-down"></i>
                    </a>
                </h4>
            </div>
            <div id="admin-info-form-additional" class="panel-collapse collapse<?=
                    ($model->meta_description||$model->keywords||$model->images||$model->params||$model->type_id||$model->date||$model->h1||$model->meta_title)?' in':''
                ?>">
                <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        $items=[''=>'']+ArrayHelper::map(Type::find()->all(), 'type_id', 'title');
                        ?>
                        <?= $form->field($model, 'type_id')->dropDownList($items) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(),[
                            'options'=>['class'=>'form-control'],
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6"><?= $form->field($model, 'h1')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-sm-6"><?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?></div>
                </div>
                <?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'images')->widget(FileSelectInput::className(),[
                    'name'=>'Info[images][]',
                    'value'=>  explode("\n", $model->images),
                    'multiple'=>true,
                    'multipleCount'=>3
                    ]) ?>
                <?php /* FileSelectInput::widget([
                    'name'=>'Info[images][]',
                    'value'=>  explode("\n", $model->images),
                    'multiple'=>true,
                    'multipleCount'=>3
                    ]) */?>

                </div>
            </div>
        </div>
    </div>




    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
