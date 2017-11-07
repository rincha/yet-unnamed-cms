<?php
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $modules Array */
/* @var $m_id string */
/* @var $c_id string */
/* @var $a_id string */
/* @var $term string */

?>
<div class="admin-default-links-nav">
    <ul class="breadcrumb">
        <li><?= Html::a(Yii::t('app', 'Modules'),['links'])?></li>
        <?php if (isset($m_id) && $m_id!==null) { ?>
        <li><?= Html::a(Html::encode($modules[$m_id]['moduleName']),['links','m_id'=>$m_id])?></li>
        <?php } ?>
        <?php if (isset($c_id) && $c_id!==null) { ?>
        <li><?= Html::a(Html::encode($modules[$m_id]['controllers'][$c_id]['name']),['links','m_id'=>$m_id,'c_id'=>$c_id])?></li>
        <?php } ?>
        <?php if (isset($a_id) && $a_id!==null) {
            $action=$modules[$m_id]['controllers'][$c_id]['actions'][$a_id];
            ?>
        <li><?= Html::a(Html::encode(is_string($action)?$action:$action['name']),['links','m_id'=>$m_id,'c_id'=>$c_id, 'a_id'=>$a_id])?></li>
        <?php } ?>
    </ul>
    <div class="input-group input-group-sm">
        <span class="input-group-addon">URL: </span>
        <?= Html::textInput('url',null,['class'=>'form-control','placeholder'=>'URL']) ?>
    </div>
</div>
<?php
$this->registerJs('$(".admin-default-links-nav input").on("change blur",function(){'
. 'window.top.adminApi.linkSelect(JSON.parse($(this).attr("data-action")));'
. '});');
?>
