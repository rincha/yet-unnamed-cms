<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $sessions Array */

$this->title = Yii::t('app/user', 'Sessions');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Account'),'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app/user', 'Sessions')];
?>
<div class="view-user-default-index">
    <h1><?= $this->title ?></h1>
    <?=
    yii\grid\GridView::widget([
        'dataProvider' => new yii\data\ArrayDataProvider(['allModels'=>$sessions]),
        'columns' => [
            'ip',
            'user_agent',
            'created_at',
            'updated_at',
            array(
                'attribute'=>'expire',
                'value'=>function($data){
                    return date('Y-m-d H:i:s',$data['expire']);
                },
            ),
        ],
    ])
    ?>

</div>
