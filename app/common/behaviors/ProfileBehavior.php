<?php
namespace app\common\behaviors;
/**
 *
 * @author rincha
 */
class ProfileBehavior extends \yii\base\Behavior {

    public $profiles;

    public function attach($owner){
        parent::attach($owner);
        if ((\Yii::$app instanceof \yii\web\Application) && \Yii::$app->user->profiles) {
            foreach (\Yii::$app->user->profiles as $id=>$profile) {
                $this->profiles[]=$id;
                $this->owner->attachBehavior('bprofile'.ucfirst($id), new $profile['behavior']);
            }
        }
    }
}
