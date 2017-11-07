<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $menu app\modules\menu\models\Menu */

$this->title = Yii::t('menu', 'Menu items') . ': ' . $menu->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Меню', 'url' => ['index']];
$this->params['breadcrumbs'][] = $menu->name . ': ' . Yii::t('menu', 'Menu items')
?>
<div class="menu-admin-items">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Add'), ['create-item', 'id' => $menu->menu_id], ['class' => 'btn btn-success btn-sm']) ?>
    </p>

    <?php
    $render = function($parent_id) use (&$render, &$menu) {
        $items = $menu->getItemsLevel($parent_id);
        ?><ul class="list-without-markers"><?php
            foreach ($items as $item) {
                ?><li>
                    <div class="item row" data-id="<?= $item->menu_item_id ?>">
                        <div class="col-xs-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-btn">
                                    <?php
                                    if ($item->has_children)
                                        echo Html::button(Html::tag('span', '', ['class' => 'glyphicon glyphicon-folder-close']), ['class' => 'btn btn-default folder']);
                                    else
                                        echo Html::button(Html::tag('span', '', ['class' => 'glyphicon glyphicon-file']), ['class' => 'btn btn-default', 'disabled' => 'disabled']);
                                    ?>
                                    <button type="button" class="btn btn-default up"><span class="glyphicon glyphicon-arrow-up"></button>
                                    <button type="button" class="btn btn-default down"><span class="glyphicon glyphicon-arrow-down"></button>
                                </span>
                                <input name="MenuItem[sort_order]" value="<?= $item->sort_order ?>" class="form-control">
                                <span class="input-group-btn">
                                    <?=
                                    \yii\bootstrap\Button::widget([
                                        'encodeLabel' => false,
                                        'label' => '<span class="glyphicon glyphicon-refresh">',
                                        'options' => ['class' => 'btn btn-default refresh', 'data-loading-text' => '<img class="loader" src="/img/ajax-loader.gif">'],
                                    ]);
                                    ?>	
                                </span>			
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <?=
                            Html::a(
                                    Html::encode($item->name), ['update-item', 'id' => $item->menu_item_id], ['class' => '']
                            );
                            ?>
                            &nbsp; &nbsp; &nbsp;
                    <?= Html::a('<span class="glyphicon glyphicon-link"></span>', $item->urlTo); ?>
                    <?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['delete-item', 'id' => $item->menu_item_id], ['class' => 'pull-right', 'data-method' => 'post']); ?>
                        </div>
                    </div>
                    <?php
                if ($item->has_children)
                    $render($item->menu_item_id);
                ?></li><?php
            }
            ?></ul><?php
    };
    $render(null);
        ?>
</div>
<?php $form = ActiveForm::begin(['id' => 'update-item-form']); ?>
<?= Html::input('hidden', 'MenuItem[sort_order]', '', ['id' => 'input-menu-item-sort-order']) ?>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs(
        '$(".menu-admin-items ul button.folder").click(function(){
	var ul=$(this).parents("li:first").find("ul");
	
	if ($(ul).is(":visible")) {
		$(this).parents("li:first").find("ul").slideUp();
		$(this).find("span.glyphicon").removeClass("glyphicon-folder-open").addClass("glyphicon-folder-close");
	}
	else {
		$(this).parents("li:first").find("ul").slideDown();
		$(this).find("span.glyphicon").removeClass("glyphicon-folder-close").addClass("glyphicon-folder-open");
	}	
});
$(".menu-admin-items ul button.up").click(function(){
	$(this).button("loading");
	var val_el=$(this).parents(".item:first").find(\'input[name*="sort_order"]\');
	$(val_el).val(parseInt($(val_el).val())-1);
	$(this).button("reset");
	$(this).parents(".item:first").find("button.refresh").click();
});
$(".menu-admin-items ul button.down").click(function(){
	$(this).button("loading");
	var val_el=$(this).parents(".item:first").find(\'input[name*="sort_order"]\');
	$(val_el).val(parseInt($(val_el).val())+1);
	$(this).button("reset");
	$(this).parents(".item:first").find("button.refresh").click();
});
$(".menu-admin-items ul button.refresh").click(function(){
	$(this).button("loading");
	var id=$(this).parents(".item:first").attr("data-id");
	var val=$(this).parents(".item:first").find(\'input[name*="sort_order"]\').val();
	$("#input-menu-item-sort-order").val(val);
	$.ajax({
		url: "' . \yii\helpers\Url::to(['update-item']) . '?id="+id,
		type: "post",
		data: $("#update-item-form").serialize()
	}).done(function(data){
		$(".menu-admin-items").replaceWith($(data).find(".menu-admin-items"));
	});
});
', \yii\web\View::POS_READY
);
?>
