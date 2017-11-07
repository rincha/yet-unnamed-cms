<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $types Array */

$this->title = Yii::t('app/widgets', 'Select type');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/widgets', 'Widgets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="widget-select-type">

    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="list-unstyled">
    <?php foreach ($types as $type=>$params) { ?>
        <li><?= Html::a(Yii::t('app/widgets', $params['name']),['create','type'=>$type]); ?></li>
    <?php } ?>
    </ul>

</div>
