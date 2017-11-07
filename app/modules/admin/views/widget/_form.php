<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\widgets\tinymce\Tinymce;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Widget */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="widget-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->hasErrors()) { ?>
        <div class="alert alert-danger"><?= Html::errorSummary($model) ?></div>
    <?php } ?>

    <div class="row">
        <div class="col-sm-6"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-sm-6"><?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="row">
        <div class="col-sm-6"><?= $form->field($model, 'position')->dropDownList($model->positionsList()) ?></div>
        <div class="col-sm-6"><?= $form->field($model, 'sort_order')->textInput(['type'=>'number']) ?></div>
    </div>

    <?= $model->wgt->renderAdminView($model, $form); ?>

    <?php if (ArrayHelper::getValue(Yii::$app->params['widgets']['items'][$model->type], 'hasContent', true)) { ?>
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
    <?php } ?>

    <?= \yii\bootstrap\Collapse::widget([
        'items'=>[
            [
                'label'=>  Yii::t('app/widgets', 'Display settings'),
                'content'=>$form->field($model, 'allow')->textarea(['rows' => 6]).
                    $form->field($model, 'deny')->textarea(['rows' => 6]),
                'contentOptions' => ['class' => $model->allow||$model->deny?'in':null]
            ]
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
