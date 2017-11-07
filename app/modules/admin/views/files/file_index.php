<?php

use yii\helpers\Html;
use app\common\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\files\models\FolderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $folder app\modules\files\models\Folder */

$this->title = Yii::t('files', 'Folders').' :: '.$folder->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('files', 'Folders'), 'url' => ['index']];
if ($folder->parent) {
    $this->params['breadcrumbs'][] = ['label'=>$folder->parent->name, 'url'=>['/admin/files/index','parent_id'=>$folder->parent->folder_id]];
}
$this->params['breadcrumbs'][] = $folder->name;
$this->params['breadcrumbs'][] = Yii::t('files', 'Files');
?>
<div class="file-index">

    <h1><?= Yii::t('files', 'Folder') ?>: <?= Html::encode($folder->name) ?></h1>

    <div class="row form-group">
        <div class="col-xs-6">
            <div class="row">
                <?= Html::beginForm(['api-create-file','id'=>$folder->folder_id,'inframe'=>1], 'post', ['enctype'=>'multipart/form-data','id'=>'tmp_form_api_create','target'=>'tmp_frame_api_create']); ?>
                <div class="col-xs-12">
                    <?= app\common\widgets\FileInputGroup\FileInputGroup::widget([
                        'fileInputName'=>'Filedata[]',
                        'fileInputOptions'=>['multiple'=>'true'],
                        'submitButton'=>true,
                    ]) ?>
                </div>
                <?= Html::endForm(); ?>
            </div>
        </div>
        <div class="col-xs-6"><?= Html::a(Yii::t('files','Advanced upload').' '.yii\bootstrap\Html::icon('menu-right'), ['create-file','folder_id'=>$folder->folder_id], ['class' => 'btn btn-success']) ?></div>
    </div>
    <?php \yii\widgets\Pjax::begin();
    $dataProvider->sort->defaultOrder=['name'=>SORT_ASC];
    ?>
    <?= GridView::widget([
        'id'=>'tmp_grid_files',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label'=>'',
                'attribute'=>'type',
                'value'=>function($model){
                    if (strpos($model->type, 'image')===0)
                        return Html::tag ('div',Html::img($model->getTmb('50s50'),['class'=>'img-responsive']),['style'=>'width:50px; height:50px;']);
                    else
                        return $model->type;
                },
                'filter'=>false,
                'format'=>'raw',
            ],
            'name',
            'ext',
            'url',
            [
                    'class' => 'app\common\grid\ActionColumn',
                    'defaultButtonsActions'=>[
                        'delete'=>'delete-file',
                        'update'=>'update-file',
                    ],
                    'buttons'=>[
                           'view' => function ($url, $model, $key) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $model->url,['target'=>'_blank','data-pjax'=>"0", 'class'=>'btn btn-sm btn-default']);
                            },
                    ],
                    'contentOptions'=>['style'=>'white-space:nowrap;']
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
<?php
yii\bootstrap\Modal::begin([
    'id'=>'tmp_modal_api_create',
    'header' => 'Загрузка файлов',
    //'toggleButton' => ['label' => 'click me'],
    'size'=>  yii\bootstrap\Modal::SIZE_LARGE,
    'clientOptions'=>[
        'show'=>false,
    ],
]);
echo '<div id="tmp_loader_api_create"><span class="fa fa-cog fa-spin"></span> Подождите, идет загрузка ...</div>';
echo yii\bootstrap\Html::tag('iframe','',['frameborder'=>0,'width'=>'100%','height'=>400,'name'=>'tmp_frame_api_create','id'=>'tmp_frame_api_create','style'=>'']);
yii\bootstrap\Modal::end();

$this->registerJs('$(document).ready(function(){
$("#tmp_form_api_create").submit(function(){
    $("#tmp_frame_api_create").load(function(){
        $("#tmp_loader_api_create").hide();
        $("#tmp_frame_api_create").show();
        $("#tmp_grid_files").yiiGridView("applyFilter");
    });
    $("#tmp_loader_api_create").show();
    $("#tmp_frame_api_create").hide();
    $("#tmp_modal_api_create").modal("show");
});
});');
?>