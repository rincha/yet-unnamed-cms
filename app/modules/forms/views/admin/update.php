<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\forms\models\Form */

$this->title = 'Изменить форму: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label'=>'Администрирование', 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Формы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->form_id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="form-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
