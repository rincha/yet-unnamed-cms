<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Collapse;
use app\modules\admin\widgets\LinksSelectWidget;

/* @var $this yii\web\View */
/* @var $menu app\modules\menu\models\Menu */
/* @var $model app\modules\menu\models\MenuItem */
/* @var $form yii\widgets\ActiveForm */
$action=$model->isNewRecord?(Yii::t('app', 'Create')):(Yii::t('app', 'Update'));
$this->title = Yii::t('menu', 'Menu item') . ': '.$action;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'Menu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $menu->name, 'url' => ['items', 'id' => $menu->menu_id]];
$this->params['breadcrumbs'][] = $action;
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="menu-admin-item">
    <div class="menu-form">

        <?php $form = ActiveForm::begin(); ?>
        <?php $list = ['' => '']; ?>
        <?= $form->field($model, 'parent_id')->dropDownList($list + $menu->getItemsList()) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

        <div class="form-group">
            <?= Html::label($model->getAttributeLabel('url'), 'tmp-links-select-fake') ?>
            <div class="input-group" id="tmp-links-select-cont">
                <?= LinksSelectWidget::widget([
                    'clientAfterSelect'=>'function(data){'
                    . 'var tmp="#'.Html::getInputId($model, 'name').'";'
                    . 'if (!$(tmp).val()) {$(tmp).val(data.text);}'
                    . '$("#'.Html::getInputId($model, 'url').'").val(data.url);'
                    . '$("#'.Html::getInputId($model, 'controller_id').'").val(data.controller);'
                    . '$("#'.Html::getInputId($model, 'action_id').'").val(data.action);'
                    . 'if (typeof(data.params) != "undefined") {'
                    . '$("#'.Html::getInputId($model, 'params').'").val(jQuery.param(data.params));'
                    . '}'
                    . '};'
                ]) ?>
                <?= Html::activeTextInput($model,'url',['class'=>'form-control']) ?>
                <span class="input-group-btn">
                    <?= Html::button(Yii::t('app', 'Select link'), ['class'=>'btn btn-primary', 'data-toggle'=>'modal', 'data-target'=>'#tmp-links-select-cont .modal'])?>
                </span>
            </div>
            <?= Html::error($model, 'url') ?>
        </div>

        <?= Collapse::widget([
            'items'=>[
                [
                    'label'=>  Yii::t('app', 'Extended properties'),
                    'content' =>
                        $form->field($model, 'controller_id')->textInput(['maxlength' => 255]).
                        $form->field($model, 'action_id')->textInput(['maxlength' => 255]).
                        $form->field($model, 'params')->textInput(['maxlength' => 255]),
                    'contentOptions' => ['class' => $model->hasErrors()?'in':'']
                ]
            ],
        ]) ?>

        <?= $form->field($model, 'sort_order')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'icon')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($model, 'image')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($model, 'css_class')->textInput(['maxlength' => 64]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>