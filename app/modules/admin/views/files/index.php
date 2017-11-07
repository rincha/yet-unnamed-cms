<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\files\models\Folder;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\files\models\FolderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $parent app\modules\files\models\Folder */
/* @var $parent_id integer */

$this->title = Yii::t('files', 'Folders');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];

if ($parent) {
    $this->params['breadcrumbs'][] = ['label'=>Yii::t('files', 'Folders'), 'url'=>'/admin/files/index'];
    if ($parent->parent) {
        $this->params['breadcrumbs'][] = ['label'=>$parent->parent->name, 'url'=>['/admin/files/index','parent_id'=>$parent->parent->folder_id]];
    }
    $this->params['breadcrumbs'][] = $parent->name;
}
else {
    $this->params['breadcrumbs'][] = Yii::t('files', 'Folders');
}
?>
<div class="folder-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create','parent_id'=>$parent_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			[
				'label'=>'',
				'value'=>function($model){
					if ($model->childs) {
						$res=Html::a('<span class="glyphicon glyphicon-folder-close"></span>',['index','parent_id'=>$model->folder_id],['class'=>'btn btn-default btn-xs']);
					}
					else {
						$res='';//Html::a('<span class="glyphicon glyphicon-file text-muted"></span>','#',['class'=>'btn btn-default','disabled'=>'disabled']);
					}
					return $res;
				},
				'format'=>'html',
			],
            'name',
            'type',			
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
