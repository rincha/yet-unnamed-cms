<?php
use yii\widgets\DetailView;
use yii\bootstrap\Nav;
use app\modules\menu\widgets\WgtMenu;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->title = Yii::t('app/user', 'Account');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Account')];
?>
<div class="view-user-default-index">
    <h1><?= $this->title ?></h1>

    <?php
    $menu=  WgtMenu::widget(['menu'=>'u.default.index','listOptions'=>['class'=>'nav nav-pills']]);
    if ($menu) {
        echo $menu;
    }
    else {
        echo Nav::widget([
        'items'=>[
            [
                'label' => Yii::t('app/user', 'Profiles'),
                'url' => ['/u/profile/index'],
            ],
            [
                'label' => Yii::t('app/user', 'Connected accounts'),
                'url' => ['authentications'],
            ],
            [
                'label' => Yii::t('app/user', 'Security'),
                'url' => ['security'],
            ],
            [
                'label' => Yii::t('app/user', 'Sessions'),
                'url' => ['sessions'],
            ],
        ],
        'options' => ['class' =>'nav-pills']
    ]);
    }
    ?>

    <?=
    DetailView::widget([
        'model' => $user,
        'attributes' => [
            'username',
            'statusName',
            'created_at',
            'updated_at',
        ],
    ])
    ?>

</div>
