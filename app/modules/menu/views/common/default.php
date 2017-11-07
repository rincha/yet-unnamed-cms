<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */
/* @var $class string */

$render=function($parent_id,$depth=0) use (&$render, &$model, &$class){
	$items=$model->getItemsLevel($parent_id);
	?><ul<?php if ($depth==0 && $class) echo ' class="'.$class.'"';?>><?php
		foreach ($items as $item) {
			?><li>
			<?= Html::a(
					Html::encode($item->name),
					$item->urlTo,
					['class'=>'']
				); ?>
			<?php
			if ($item->has_children) $render($item->menu_item_id,$depth+1);
			?></li><?php
		}
	?></ul><?php
};
$render(null);
?>