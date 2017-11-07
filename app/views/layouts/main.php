<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginContent('@app/views/layouts/columns.php'); ?>
<div class="thm-content">
    <div class="thm-content-wrap">
    <div class="container-fluid">        
        <?= \app\common\widgets\FlashMessages\FlashMessages::widget(); ?>
        <div class="row form-group">
            <div class="col-xs-12">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= $content ?>
            </div>
        </div>        
    </div>
    </div>
</div>
<?php $this->endContent(); ?>
