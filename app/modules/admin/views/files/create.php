<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Folder */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>  Yii::t('files', 'Folder')]);
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('files', 'Folders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="folder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
