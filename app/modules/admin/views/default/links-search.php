<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $modules Array */
/* @var $m_id string */
/* @var $c_id string */
/* @var $a_id string */
/* @var $term string */
/* @var $dp yii\data\ActiveDataProvider */
?>
<?= $this->render('links-nav',['modules'=>$modules, 'm_id'=>$m_id, 'c_id'=>$c_id, 'a_id'=>$a_id]) ?>
<div class="admin-default-links-search">
    <h2>Выберите элемент</h2>
    <?php
    $action=$modules[$m_id]['controllers'][$c_id]['actions'][$a_id];
    $action_data=[
        'controller'=>$m_id.'/'.$c_id,
        'action'=>$a_id,
        'route'=>'/'.$m_id.'/'.$c_id.'/'.$a_id,
    ];
    $columns=ArrayHelper::getValue($action, 'columns');
    if (!$columns) {
        $columns=['name'=>'name'];
    }
    $columns['actions']=[
        'value'=>function($model)use(&$action_data){
            $data=$action_data;
            if (method_exists($model, 'getUrlArr')) {
                $url=$model->getUrlArr();
                $route=$model->getUrlArr()[0];
                $params=array_slice($model->getUrlArr(), 1);
            }
            else {
                $route='/'.$action_data['controller'].'/'.$action_data['action'];
                $params=['id'=>$model->primaryKey];
                $url=array_merge([$route],$params);
            }
            $data['url']=Url::to($url);
            $data['params']=$params;
            $data['text']=$model->name;
            return Html::button('Выбрать', ['class'=>'btn btn-success btn-xs', 'data-action'=>Json::encode($data)]);
        },
        'format'=>'raw',
        'contentOptions'=>['class'=>'text-right', 'style'=>'width:1%'],
    ];
    ?>
    <?= Html::beginForm(['links'], 'get'); ?>
    <?= Html::hiddenInput('m_id',$m_id) ?>
    <?= Html::hiddenInput('c_id',$c_id) ?>
    <?= Html::hiddenInput('a_id',$a_id) ?>
    <div class="input-group input-group-sm">
        <span class="input-group-addon input-group-addon-sm"><?= Yii::t('app', 'Search') ?>:</span>
        <?= Html::textInput('term',$term,['class'=>'form-control']) ?>
        <span class="input-group-btn">
            <?= Html::button('<i class="fa fa-search"></i>',['class'=>'btn btn-primary']) ?>
        </span>
    </div>
    <?= Html::endForm() ?>
    <?=
    GridView::widget([
        'tableOptions'=>['class'=>'table table-condensed table-hover table-bordered'],
        'dataProvider'=>$dp,
        'columns'=>$columns,
    ])
    ?>
</div>
<?php
$this->registerJs('$(".admin-default-links-search button.btn-success").click(function(){'
        . 'var data=$(this).attr("data-action");'
        . 'var url=JSON.parse(data)["url"];'
        . '$(".admin-default-links-nav input").attr("data-action",data);'
        . '$(".admin-default-links-nav input").val(url);'
        . '$(".admin-default-links-nav input").change();'
        . '});');
?>
