<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div class="site-login-form">
    <?php $form = ActiveForm::begin(['action'=>['/user/login']]);?>

    <?= $form->field($model, 'username',['template'=>"{label}\n{input}\n{hint}\n{error}"])->textInput() ?>

    <?= $form->field($model, 'password',['template'=>"{label}\n{input}\n{hint}\n{error}"])->passwordInput(['data-toggle'=>'password']) ?>

    <?php
    if ($model->scenario=='login-captcha') {
        ?>
        <div class="form-group">
        <?= $form->field($model, 'verify_code', ['enableClientValidation' => false])
                ->widget(Captcha::className(), ['captchaAction' => ['/site/captcha']]) ?>
        </div>
        <?php
    }
    ?>

    <div class="form-group">
            <div class="checkbox">
                    <label for="input-rememberMe">
                    <?php
                    echo Html::checkbox('rememberMe', !!Yii::$app->request->post('rememberMe'), ['id'=>'input-rememberMe']);
                    echo Yii::t('app/user', 'Remember me')
                    ?>
                    </label>
            </div>
    </div>

    <div class="form-group">
		<?= Html::submitButton(Yii::t('app/user', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="form-group">
            <?= Html::a(Yii::t('app/user', 'Register'),'/user/register') ?> |
            <?= Html::a(Yii::t('app/user', 'Activation'),['/user/activate-send']) ?> |
            <?= Html::a(Yii::t('app/user', 'Forgot password?'),'/user/restore') ?>
    </div>
</div>

