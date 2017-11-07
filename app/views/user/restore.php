<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\UserRestore */

$this->title = Yii::t('app/user', 'Restore access');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin();?>

    <div class="row">
        <div class="col-xs-4">
            <?php
            $types=[];
            foreach (Yii::$app->user->authentications['types'] as $k => $type) {
                $className=Yii::getAlias('\app\models\authentication\\' . ucfirst($type['id']));
                if (in_array('sendRestore',$className::allowedMethods())) {
                    $types[$k]=Yii::t('app/user', $type['name']);
                }
            }
            echo $form->field($model, 'type')->dropDownList($types);
            ?>
        </div>
        <div class="col-xs-8">
            <?=
            $form->field($model, 'uid')->textInput();
            ?>
        </div>
    </div>

    <div class="form-group">
    <?= $form->field($model, 'verify_code', ['enableClientValidation' => false])->widget(Captcha::className(), ['captchaAction' => '/site/captcha']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/user', 'Restore'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs('$("#'.Html::getInputId($model, 'type').'").on("change",function(){'
        . '$(\'label[for="'.Html::getInputId($model, 'uid').'"]\').text($(this).val());'
        . '});'
        . '$("#'.Html::getInputId($model, 'type').'").change();');

