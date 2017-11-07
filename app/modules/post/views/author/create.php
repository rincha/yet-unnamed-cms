<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Post */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('post', 'My posts'), 'url'=>['/post/author/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="post-create">

    <h1><?= Html::encode(Yii::t('post', 'Create post')) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>