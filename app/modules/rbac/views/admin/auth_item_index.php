<?php

use yii\helpers\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\modules\rbac\models\AuthItem */

$this->title = Yii::t('rbac', 'Auth Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rbac', 'Create'), ['auth-item-create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('rbac', 'Create default roles'), ['auth-default-roles-create'], ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('rbac', 'Create controller roles'), ['auth-controller-roles-create'], ['class' => 'btn btn-default']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $model,
        'columns' => [
            'name',
            [
				'attribute'=>'type',
				'value'=>function($model){return $model->typeLabel;},
				'filter'=>app\modules\rbac\models\AuthItem::getTypesLabels(),
			],
            'description:ntext',
            'rule_name',
            'data:ntext',
            [
				'class' => 'yii\grid\ActionColumn',
				'urlCreator'=>function($action, $model, $key, $index){
					switch ($action) {
						case 'view': return \yii\helpers\Url::to(['auth-item-view','id'=>$model->name]); break;
						case 'update': return \yii\helpers\Url::to(['auth-item-update','id'=>$model->name]); break;
						case 'delete': return \yii\helpers\Url::to(['auth-item-delete','id'=>$model->name]); break;
						default: return \yii\helpers\Url::to('#'); break;
					}
				},
			],
        ],
    ]); ?>

</div>
