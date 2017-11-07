<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\ProfilePerson */


$this->title = Yii::t('user/common', 'Update person profile');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Account'),'url'=>'/u/default/index'];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Profiles'),'url'=>'/u/profile/index'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('user/common', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="profile-person-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
