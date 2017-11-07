<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $form app\modules\forms\models\Form */
/* @var $model app\modules\forms\models\Form */

$this->title = Yii::t('forms','Fields').': '.Yii::t('app','Update');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms','Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $form->name, 'url' => ['view', 'id' => $form->form_id]];
$this->params['breadcrumbs'][] = 'Изменить поле: '.$model->title;
?>
<div class="form-create">

    <h1><?= Yii::t('app','Update {modelClass}:',['modelClass'=>Yii::t('forms','Field')])?> [<?= Html::encode($model->title) ?>]</h1>

    <?= $this->render('field_form', [
        'model' => $model,
    ]) ?>

</div>
