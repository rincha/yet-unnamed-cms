<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\news\models\NewsType */
$this->title = Yii::t('app', 'Update {modelClass}: ',['modelClass'=>Yii::t('news', 'News type')]);
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('news','News'), 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('news', 'News types'), 'url' => ['type-index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="news-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
