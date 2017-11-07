<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\forms\models\Form */

$this->title = Yii::t('forms','Forms').': '.Yii::t('app','Create');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms','Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Create');
?>
<div class="form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
