<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\AuthItem */

$this->title = Yii::t('rbac', 'Update {type}: ', [
    'type' => $model->typesLabels[$model->type],
]) . ' ' . $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Auth Items'), 'url' => ['auth-item-index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['auth-item-view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('rbac', 'Update');
?>
<div class="auth-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('auth_item_form', [
        'model' => $model,
    ]) ?>

</div>
