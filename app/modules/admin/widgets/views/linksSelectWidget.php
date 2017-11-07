<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $id string */
/* @var $afterSelect string */

$this->registerJs('adminApi.linkSelect=function(data){$("#'.$id.'").find(".btn-primary").attr("data-url",JSON.stringify(data));};',  yii\web\View::POS_END);
$this->registerJs('$("#'.$id.'").find(".btn-primary").click(function(){'
        . 'var callback='.$afterSelect.';'
        . 'callback(JSON.parse($(this).attr("data-url")));'
        . '$("#'.$id.'").modal("hide");'
        . '});',  yii\web\View::POS_END);
?>
<div class="modal fade" tabindex="-1" role="dialog" id="<?= $id ?>">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('app', 'Select link')?></h4>
      </div>
      <div class="modal-body">
            <div class="admin-wgt-link-select">
            <iframe class="tmp-auto-height" src="<?= Url::to(['/admin/default/links']) ?>" frameborder="0" style="overflow: auto; height: 100%; width: 100%;" height="100%" width="100%"></iframe>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Close')?></button>
        <button type="button" class="btn btn-primary"><?= Yii::t('app', 'Select')?></button>
      </div>
    </div>
  </div>
</div>



