<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\post\models\Post;
use app\modules\post\models\Comment;
use app\modules\post\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\SettingsForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('post','Posts settings');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app','Administration'), 'url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('post','Posts'), 'url'=>['/post/admin/index']];
$this->params['breadcrumbs'][] = Yii::t('post','Settings');
?>
<div class="post-admin-settings">

    <h1><?= Html::encode(Yii::t('post', 'Posts settings')) ?></h1>

    <?php $form = ActiveForm::begin([
        'enableClientValidation'=>true,
    ]); ?>

    <?= $form->field($model, 'displayStatus')->checkboxList(Post::getStatusList()) ?>

    <?= $form->field($model, 'commentDisplayStatus')->checkboxList(Comment::getStatusList()) ?>

    <?= $form->field($model, 'commentGuestStatus')->dropDownList(Comment::getStatusList()) ?>

    <?= $form->field($model, 'commentUserStatus')->dropDownList(Comment::getStatusList()) ?>

    <?= $form->field($model, 'commentAnswerPermit')->dropDownList([
        Module::ANSWER_PERMIT_GUEST=>Yii::t('post', 'Guests'),
        Module::ANSWER_PERMIT_USER=>Yii::t('post', 'Users'),
        Module::ANSWER_PERMIT_AUTHOR=>Yii::t('post', 'Author'),
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
