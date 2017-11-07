<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Folder */

$this->title = Yii::t('app', 'Update {modelClass}: ', ['modelClass'=>  Yii::t('files', 'Folder')]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('files', 'Folders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->folder_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="folder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
