<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $log Array */

$this->title = Yii::t('rbac', 'Create all allowed rules');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rules'), 'url' => ['auth-rule-index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
	foreach ($log as $row) {
		?>
	<p class="bg-<?= $row[0] ?>"><?= Html::encode($row[1]) ?></p>
		<?php
	} ?>

</div>
