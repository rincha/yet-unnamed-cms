<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\modules\rbac\models\AuthItem;
use app\modules\rbac\models\AuthAssignment;
use app\models\User;


/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-item-view">

    <h1><?= Html::encode($this->title) ?></h1>
	
	<?php
	$dataProvider=new yii\data\ActiveDataProvider([
		'query' => AuthAssignment::find()->where(['user_id'=>$model->id])->with('authItem'),
		'pagination' => ['pageSize' => 50,],
	]);
	?>
	<?php \yii\widgets\Pjax::begin(['linkSelector'=>'a[data-method="post"]']); ?>
	<?= GridView::widget([
		'caption'=>  Yii::t('rbac','Assignments'),
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'attribute'=>'authItem.name',
				'format'=>'html',
				'value'=>function($model, $key, $index, $column){
					return Html::a(
							$model->authItem->name,  
							\yii\helpers\Url::to(['auth-item-view','id'=>$model->authItem->name])
					);
				},
			],
			[
				'attribute'=>'authItem.type',
				'value'=>function($model, $key, $index, $column){
					return $model->authItem->typesLabels[$model->authItem->type];
				},
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' =>'{delete}',
				'urlCreator'=>function($action,$data) use(&$model) {
					return \yii\helpers\Url::to(['assignment-delete','id'=>$data->item_name,'user_id'=>$model->id]);
				}
			],
		],
	]); ?>
	<?php \yii\widgets\Pjax::end(); ?>
	
	<?php echo Html::beginForm(); 
	$data=AuthItem::find()->
			select('t.*, a.user_id')->
			from(AuthItem::tableName().' t')->
			join('LEFT JOIN', AuthAssignment::tableName().' a','(a.user_id=:user_id && a.item_name=t.name)',[':user_id'=>$model->id])->
			where('a.user_id IS NULL && t.type='.yii\rbac\Item::TYPE_ROLE)->
			groupBy('t.name')->
			all();
	;
	?>
	<div class="form-group">
	<?=	Html::dropDownList(
			'AuthAssigment[]',
			[],
			ArrayHelper::map($data, 'name', 'name'),
			['multiple'=>true,'class'=>'form-control']
	) ?>
	</div>
	<div class="form-group">
        <?= Html::submitButton(Yii::t('rbac', 'Add'), ['class' =>  'btn btn-success' ]) ?>
    </div>
    <?php echo Html::endForm(); ?>

</div>
