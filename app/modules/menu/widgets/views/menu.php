<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */
/* @var $widget app\models\Widget */
/* @var $class string */
/* @var $listOptions Array */
/* @var $id string */

$render=function($parent_id,$depth=0) use (&$render, &$model, &$listOptions){
	$items=$model->getItemsLevel($parent_id);
        $listItems='';
        foreach ($items as $item) {
                $linkOptions=[];
                if (preg_match('/^http(s)?:\/\//ui', Url::to($item->urlTo)) && !StringHelper::startsWith(Url::to($item->urlTo), Url::base(true),false)) {
                    $linkOptions['target']='_blank';
                }
                if (Url::to($item->urlTo)==Url::current()) {
                    $linkOptions['class']='active';
                }
                $listItems.=Html::tag(
                    'li',
                    Html::a(
                        ($item->icon?Html::tag('span','',['class'=>$item->icon]):'').
                        Html::encode($item->name),
                        $item->urlTo,
                        $linkOptions
                    ).
                    ($item->has_children?$render($item->menu_item_id,$depth+1):'')
                );
        }
	return Html::tag('ul', $listItems, ($depth===0&&$listOptions)?$listOptions:[]);
};
?>
<div class="<?= Html::encode($class)?>" id="<?= $id ?>">
    <?php if (isset($widget)&&$widget->title || !isset($widget)&&$model->title) { ?>
    <div class="title"><?= Html::encode(isset($widget)?$widget->title:$model->title) ?></div>
    <?php } ?>
    <div class="content">
        <?= $render(null); ?>
    </div>
</div>