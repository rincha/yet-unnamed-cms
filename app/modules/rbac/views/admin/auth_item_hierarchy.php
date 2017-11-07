<?php
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\AuthItem */
?>
<h3><?= Yii::t('rbac','Parents hierarchy') ?></h3>
<?php
/*$parents=function($child) use (&$parents) {
	$items=[];
	$models=\app\modules\rbac\models\AuthItemChild::find()->where(['child'=>$child])->with(['childItem','parentItem'])->all();
	foreach ($models as $model) {
		$item=['label' => $model->parent];
		$p=$parents($model->parent);
		if ($p) $item['items']=$p;
		$items[]=$item;
	}
	return $items;
};*/
$parents=function($child,$chain,$o) use (&$parents) {
	/*echo '<pre>';
	echo yii\helpers\VarDumper::dump($chain);
	echo '</pre>';*/
	$items=[];
	$models=\app\modules\rbac\models\AuthItemChild::find()->where(['child'=>$child])->with(['childItem','parentItem'])->all();
	if (!$models) return $chain;
	$n=0;
	foreach ($models as $model) {
		if (count($models)>1) {
			$items[$n]=$chain;
			array_unshift($items[$n], $model->parent);
		}
		else {
			$items=$chain;
			array_unshift($items, $model->parent);
		}
		$n++;
	}
	$nitems=[];
	foreach ($items as $k=>$c) {
		$nchian=$parents($c[0],$c,$o+1);
		$nitems[$k]=$nchian;
	}
	return $nitems;
};
$items=$parents($model->name,[$model->name],0);
/*echo '<pre>';
echo yii\helpers\VarDumper::dump($items);
echo '</pre>';*/

$n=1;
if ($items && is_array($items[0])) {
	foreach ($items as $chain) {
		$witems=[['label'=>Yii::t('rbac','Chain {n}',['n'=>$n])]];
		$n++;
		foreach ($chain as $item) {
			$witems[]=['label'=>$item,'url'=>  \yii\helpers\Url::to(['auth-item-view','id'=>$item])];
		}	
		echo Menu::widget([
		'items'=>$witems,
		'options'=>['style'=>'display:block; float:left']
		]);
	}
}
else {
	$witems=[];
	foreach ($items as $item) {
		$witems[]=['label'=>$item,'url'=>  \yii\helpers\Url::to(['auth-item-view','id'=>$item])];
	}
	echo Menu::widget([
		'items'=>$witems,
		'options'=>['style'=>'display:block; float:left']
	]);
}



/*
echo Menu::widget([
	'items'=>$parents($model->name,[$model->name]),
]);*/
?>