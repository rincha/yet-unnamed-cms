<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\news\models\News */

$this->title = Yii::t('app', 'Create {modelClass}',['modelClass'=>Yii::t('news', 'News item')]);
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('news', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="news-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
