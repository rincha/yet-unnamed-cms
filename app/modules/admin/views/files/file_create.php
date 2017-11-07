<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\files\models\File */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Каталоги :: '.$folder->name;
$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $folder->name, 'url' => ['view','id'=>$folder->folder_id]];
$this->params['breadcrumbs'][] = 'создать';
?>
<?php if ($model->errors) { ?><div class="alert alert-danger"><?= Html::errorSummary($model)?></div><?php } ?>
<div class="folder-form">

    <?php $form = ActiveForm::begin([
		'enableClientValidation'=>false,
		'enableAjaxValidation'=>false,
		'options'=>['enctype'=>'multipart/form-data']
	]); 
	?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
	
    <?= $form->field($model, 'new_file')->fileInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => 64]) ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
