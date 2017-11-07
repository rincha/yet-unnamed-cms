<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rbac', 'Rules');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rbac', 'Create all allowed rules'), ['auth-rule-create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
			'name',
            'data:ntext',
            [
				'class' => 'yii\grid\ActionColumn',
				'template'=>'{delete}',
				'urlCreator'=>function($action, $model, $key, $index){
					switch ($action) {
						case 'delete': return \yii\helpers\Url::to(['auth-rule-delete','id'=>$model->name]); break;
						default: return \yii\helpers\Url::to('#'); break;
					}
				},
			],
        ],
    ]); ?>

</div>
