<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\files\models\File */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('files', 'Files').' :: '.$model->name.'.'.$model->ext;
$this->params['breadcrumbs'][] = ['label' => Yii::t('files', 'Folders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->folder->name, 'url' => ['view','id'=>$model->folder->folder_id]];
$this->params['breadcrumbs'][] = Yii::t('files', 'Files').': '.$model->name.'.'.$model->ext;
?>
<?php if ($model->errors) { ?><div class="alert alert-danger"><?= Html::errorSummary($model)?></div><?php } ?>
<div class="file-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_file', [
        'model' => $model,
    ]) ?>

</div>
