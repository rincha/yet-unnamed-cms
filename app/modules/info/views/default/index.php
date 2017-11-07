<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use app\common\helpers\AppHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\info\models\InfoSearch */
/* @var $typeModel app\modules\info\models\Type */
/* @var $dataProvider yii\data\ActiveDataProvider */


if ($typeModel) {
    if (Yii::$app->getModule('info')->enableIndexAction && Yii::$app->getModule('info')->enableIndexActionWithoutType) {
        $this->params['breadcrumbs'][] = ['label' => Yii::t('info', 'Information materials'), 'url' => ['index']];
    }
    $this->params['breadcrumbs'][] = ['label' => $typeModel->title];
    $this->title = AppHelper::getPageTitle($typeModel->title, ['title'=>$typeModel->title]);
}
else {
    $this->title = AppHelper::getPageTitle(Yii::t('info', 'Information materials'), ['title'=>null]);
    $this->params['breadcrumbs'][] = Yii::t('info', 'Information materials');
}

?>
<div class="info-index">

    <h1><?= Html::encode(
             AppHelper::getPageH1($typeModel?$typeModel->title:Yii::t('info', 'Information materials'), ['title'=>$typeModel?$typeModel->title:Yii::t('info', 'Information materials')])
        ) ?></h1>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView'=>'_view',
    ]); ?>
</div>
