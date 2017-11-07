<?php

use yii\helpers\Html;
use app\common\grid\GridView;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\info\models\Info */
/* @var $relation app\modules\info\models\Relation */
/* @var $types app\modules\info\models\RelationType[] */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('info', 'Information materials').': '.Yii::t('info', 'Relations');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('info', 'Information materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->info_id]];
$this->params['breadcrumbs'][] = Yii::t('info', 'Relations');
?>
<div class="info-index">

    <h1><?= Html::encode($model->name) ?>: <?php echo Html::encode(Yii::t('info', 'Relations')) ?></h1>

    <div class="form-group">

    </div>

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <?= $form->field($relation, 'type_id')->dropDownList(yii\helpers\ArrayHelper::map($types, 'type_id', 'title')) ?>
        </div>
        <div class="col-sm-6 col-md-6">
            <?= $form->field($relation, 'slave_id')->widget(AutoComplete::className(),[
                'options'=>['class'=>'form-control'],
                'clientOptions' => [
                    'source' => \yii\helpers\Url::to(['lookup']),
                ],
            ]) ?>
        </div>
        <div class="col-sm-12 col-md-3">
            <?= $form->field($relation, 'sort_order')->textInput() ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <?php

    foreach ($types as $type) {
        echo Html::tag('h2',  Html::encode($type->title));
        $dataProvider=new yii\data\ArrayDataProvider([
            'allModels' => $model->getSlavesByType($type->type_id),
        ]);
        $pjax= Pjax::begin([
            'timeout'=>5000,
            'options'=>['class'=>'adm-pjax'],
            'id'=>'pjax-'.$type->type_id,
        ]) ;
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute'=>'slave.name',
                    'value'=>function($model){return Html::a(Html::encode($model->slave->name),['view','id'=>$model->slave_id]);},
                    'format' => 'html',
                ],
                [
                    'attribute'=>'sort_order',
                    'value'=>function($model){
                        $input=Html::activeTextInput($model, 'sort_order',['class'=>'form-control']);
                        $button=Html::button('<i class="fa fa-refresh"></i>',[
                            'class'=>'btn btn-default tmp-sort-btn',
                            'data-url'=> yii\helpers\Url::to(['relation-update','id'=>$model->relation_id]),
                            'data-pjax-id'=>'pjax-'.$model->type_id,
                        ]);
                        return Html::tag('div',
                                $input.Html::tag('span', $button, ['class'=>'input-group-btn']),
                                ['class'=>'input-group',]
                        );
                    },
                    'format'=>'raw',
                ],
                [
                    'class' => 'app\common\grid\ActionColumn',
                    'template'=>'{delete}',
                    'defaultButtonsActions' => [
                        'delete' => 'relation-delete'
                    ],
                ],
            ],
        ]);
        Pjax::end();
    }
    ?>
</div>
<?php
$this->registerJs('$(".adm-pjax").on("click",".tmp-sort-btn",function(){'
        . '$(this).button("loading");'
        . '$.ajax({'
        . 'url:$(this).attr("data-url"),'
        . 'data: {Relation:{sort_order:$(this).parents("td:first").find("input:first").val()}},'
        . 'method:"post",'
        . 'btn: this'
        . '}).done(function(){'
        . '$.pjax.reload({container:"#"+$(this.btn).attr("data-pjax-id"),timeout:5000});'
        . '$(this.btn).button("reset");'
        . '});'
        . '});');
//pjax
$this->registerJs(
    '$(document).on("pjax:start", function() {$(".adm-pjax").addClass("loading");});'
  . '$(document).on("pjax:end", function() {$(".adm-pjax").removeClass("loading");});'
);
