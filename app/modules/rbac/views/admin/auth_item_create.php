<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\AuthItem */

$this->title = Yii::t('rbac', 'Create role or permission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Auth Items'), 'url' => ['auth-item-index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('auth_item_form', [
        'model' => $model,
    ]) ?>

</div>
