<?php
use yii\helpers\Html;
use app\common\helpers\AppHelper;


/* @var $this app\common\web\View */
/* @var $model app\modules\info\models\Info */
$this->h1=$model->h1?$model->h1:null;
$this->title=$model->meta_title?$model->meta_title:$model->name;
$this->registerMetaTag([
    'name'=>'description',
    'content'=>$model->meta_description,
]);
if (Yii::$app->getModule('info')->enableIndexAction && Yii::$app->getModule('info')->enableIndexActionWithoutType) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('info', 'Information materials'), 'url'=>['index']];
}

if ($model->type && Yii::$app->getModule('info')->enableIndexAction) {
    $this->params['breadcrumbs'][] = ['label' => $model->type->title, 'url'=>$model->type->getUrlArr()];
}

$this->params['breadcrumbs'][] = $model->name;
?>
<div class="info-default-view">
    <?php
    if ($model->h1) {
        echo Html::tag('h1',  Html::encode($model->h1));
    }
    echo $model->content;
    ?>
</div>
