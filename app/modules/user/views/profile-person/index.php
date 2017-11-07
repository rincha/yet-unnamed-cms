<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\ProfilePerson */

$this->title = Yii::t('user/common', 'Profile').' '.Yii::t('user/common', 'Person');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Account'),'url'=>'/u/default/index'];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Profiles'),'url'=>'/u/profile/index'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-person-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'last_name',
            'first_name',
            'middle_name',
            [
                'attribute'=>'birthday',
                'value'=>  Yii::$app->formatter->asDate($model->birthday),
            ],
        ],
    ]) ?>


</div>
