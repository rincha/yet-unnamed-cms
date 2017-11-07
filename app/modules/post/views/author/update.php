<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Post */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('post','Post'),
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('post', 'My posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->post_id]];
$this->params['breadcrumbs'][] = Yii::t('yii', 'Update');
?>
<div class="post-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
