<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\AuthItem */
?>
			<?php
			$dataProvider=new yii\data\ActiveDataProvider([
				'query' => \app\modules\rbac\models\AuthItemChild::find()->where(['parent'=>$model->name])->with('childItem'),
				'pagination' => ['pageSize' => 20,],
			]);
			?>
			<?php \yii\widgets\Pjax::begin(); ?>
			<?= GridView::widget([
				'caption'=>  Yii::t('rbac','Auth item children'),
				'dataProvider' => $dataProvider,
				'columns' => [
					'child',
					[
						'attribute'=>'childItem.type',
						'value'=>function($model, $key, $index, $column){
							return $model->childItem->typesLabels[$model->childItem->type];
						},
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template' =>'{delete}',
						'urlCreator'=>function($action,$model) {
							return \yii\helpers\Url::to(['auth-item-child-delete','parent'=>$model->parent,'child'=>$model->child]);
						}
					],
				],
			]); ?>
			<?php \yii\widgets\Pjax::end(); ?>
