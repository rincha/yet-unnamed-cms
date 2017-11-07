<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\models\UserAuthentication;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\models\User */


$this->title = Yii::t('app/user', 'Connected accounts');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Account'),'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Connected accounts')];
?>
<div class="view-user-default-authentications">
    <h1><?= $this->title ?></h1>
    <?php
    $can_add_authentications=[];
    foreach (Yii::$app->user->authentications['types'] as $key=>$type) {
        if (!ArrayHelper::getValue($user->authentications, $key)) {
            $ua=new UserAuthentication();
            $ua->user_id=$user->id;
            $ua->type=$key;
            if ($ua->getTypeModel()->protocol) {
                $can_add_authentications[]=Html::a(
                        '<i class="'.ArrayHelper::getValue($type, 'iconClass','fa fa-'.$key).'"></i> '.Yii::t('app/user', $type['name']),
                        ['authentication-create','type'=>$key],
                        [
                            'class'=>'btn btn-success btn-xs',
                            'data'=>[
                                'method'=>'post',
                            ],
                        ]
                );
            }
            else {
                $can_add_authentications[]=Html::a(
                        '<i class="'.ArrayHelper::getValue($type, 'iconClass','fa fa-'.$key).'"></i> '.Yii::t('app/user', $type['name']),
                        ['authentication-create','type'=>$key],
                        ['class'=>'btn btn-success btn-xs']
                );
            }
        }
    }
    if ($can_add_authentications) {
        echo Yii::t('app/user', 'Add new account: ').implode(' ', $can_add_authentications);
    }
    ?>
    <?php
    foreach ($user->authentications as $authentication) {
        echo Html::tag('h2',$authentication->typeName);
        echo Html::a(Yii::t('app', 'Delete'), ['authentication-delete', 'id' => $authentication->id], [
            'class' => 'btn btn-danger',
            'disabled'=>$authentication->typeParams['required'],
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]);
        echo DetailView::widget([
            'model' => $authentication,
            'attributes' => [
                'uid',
                'statusName',
                'created_at',
                'updated_at',
            ],
        ]);
    }
    ?>
</div>