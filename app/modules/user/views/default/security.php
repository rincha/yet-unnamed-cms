<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $settings app\modules\user\models\SecuritySettings */

$this->title = Yii::t('app/user', 'Account').' :: '.Yii::t('app/user', 'Security');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Account'),'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Security')];
?>
<div class="view-user-default-security">
    <h1><?= Yii::t('app/user', 'Security') ?></h1>

    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]);?>

    <?= $form->field($settings, 'disableParallelSessions')->checkbox() ?>
    <?= $form->field($settings, 'regenerateAuthKey')->checkbox() ?>

    <div class="form-group">
    <?= yii\bootstrap\Button::widget([
        'label' => Yii::t('app/user', 'Change password'),
        'options' => ['class'=>'btn btn-default collapse in','type'=>'button','data-toggle'=>'collapse', 'data-target'=>'#tmp_password_change_collapse,#tmp_password_change_collapse_change,#tmp_password_change_collapse_cancel','id'=>'tmp_password_change_collapse_change'],
    ]) ?>
    <?= yii\bootstrap\Button::widget([
        'label' => Yii::t('app', 'Cancel'),
        'options' => ['class'=>'btn btn-default collapse','type'=>'button','data-toggle'=>'collapse', 'data-target'=>'#tmp_password_change_collapse,#tmp_password_change_collapse_change,#tmp_password_change_collapse_cancel','id'=>'tmp_password_change_collapse_cancel'],
    ]) ?>
    </div>

    <div class="collapse<?= $user->hasErrors()?' in':''?>" id="tmp_password_change_collapse">
    <?= $form->field($user, 'password',['template'=>"{label}\n{input}\n{error}",])
            ->label(Yii::t('app/user', 'Current password'))
            ->passwordInput(['data-toggle'=>'password'])
    ?>
    <?= $form->field($user, 'new_password')
            ->label(Yii::t('app/user', 'New password'))
            ->passwordInput(['data-toggle'=>'password'])
    ?>
    <?= $form->field($user, 'new_password_verify')
            ->label(Yii::t('app/user', 'New password repeat'))
            ->passwordInput(['data-toggle'=>'password'])
    ?>
    </div>

    <div class="form-group">
    <?=
    $form->field($user, 'auth_key',[
        'template'=>'{label}'
        . Html::tag(
                'div',
                '{input}'.Html::tag('span',  '<label>'.Html::input('checkbox','renew-auth-key').' заменить</label>',['class'=>'input-group-addon']),
                ['class'=>'input-group checkbox']
        )
        . '{hint}{error}',
    ])->textInput(['disabled'=>'disabled'])
    ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs('$("#'.$form->id.'").submit(function(){'
        . '$(this).find(".collapse:not(.in) input").attr("disabled","disabled");'
        . ''
        . '});', yii\web\View::POS_END, 'my-options');
?>
