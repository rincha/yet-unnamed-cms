<?php
namespace app\modules\user\behaviors;
/**
 *
 * @author rincha
 */
class ProfilePersonBehavior extends \yii\base\Behavior {    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfilePerson()
    {
        return $this->owner->hasOne(\app\modules\user\models\ProfilePerson::className(), ['user_id' => 'id']);
    }
}
