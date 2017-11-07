<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use app\common\helpers\AppHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\news\models\NewsSearch */
/* @var $typeModel app\modules\news\models\NewsType */
/* @var $dataProvider yii\data\ActiveDataProvider */


if ($typeModel) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('news', 'News'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $typeModel->title];
    $this->title = AppHelper::getPageTitle($typeModel->title, ['title'=>$typeModel->title]);
}
else {
    $this->title = AppHelper::getPageTitle(Yii::t('news', 'News'), ['title'=>null]);
    $this->params['breadcrumbs'][] = Yii::t('news', 'News');
}

?>
<div class="news-index">

    <h1><?= Html::encode(
             AppHelper::getPageH1($typeModel?$typeModel->title:Yii::t('news', 'News'), ['title'=>$typeModel?$typeModel->title:Yii::t('news', 'News')])
        ) ?></h1>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView'=>'_view',
    ]); ?>
</div>
