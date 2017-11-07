<?php

namespace app\common\widgets;
use Yii;
use yii\bootstrap\Nav;

/**
 * @author rincha
 */
class UserPanel extends \yii\base\Widget {
    
    public $cssClass='nav nav-pills';
    
    public function run() {
        $items=[];
        if ( Yii::$app->user->isGuest) {
            $items[]=['label' => Yii::t('app/user', 'Login'), 'url' => ['/user/login']];
            $items[]=['label' => Yii::t('app/user', 'Register'), 'url' => ['/user/register']];
        }
        else {
            if (isset(Yii::$app->user->identity->authentications['email'])) {
                $username=Yii::$app->user->identity->authentications['email']->uid;
            }
            else {
                $username=Yii::$app->user->identity->username;
            }
            $items_user=[];
            $items_user[]=['label' => Yii::t('app/user', 'My account'), 'url' => ['/u/default/index']];
            if (Yii::$app->user->can('Admin.Default.Index')) {
                $items_user[]=['label' => Yii::t('app', 'Administration'), 'url' => ['/admin/default/index']];
            }
            $items[]=['label' => \yii\helpers\StringHelper::truncate($username, 32), 'url' => ['/u/default/index'],'items'=>$items_user];
            $items[]=[
                'label' => Yii::t('app/user', 'Logout'),
                'url' => ['/user/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        }
        return Nav::widget([
            'options' => ['class' => $this->cssClass, 'id'=>$this->id],
            'items' => $items,
        ]);
    }
}
