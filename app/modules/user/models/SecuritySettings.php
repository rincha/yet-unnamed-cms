<?php

namespace app\modules\user\models;

use Yii;
use app\models\User;
use app\models\UserSettings;

/*
 * see application Yii::$app->params: 'user.settings'
 */

class SecuritySettings extends \yii\base\Model {

    private $_user;
    public $user_id;
    public $disableParallelSessions;
    public $regenerateAuthKey;

    public function rules() {
        $rules = [
            [['disableParallelSessions','regenerateAuthKey'], 'boolean'],
        ];
        return $rules;
    }

    public function loadFromUser() {
        $this->disableParallelSessions = $this->user->getSettingVal('security.disable.parallel.sessions');
        $this->regenerateAuthKey = $this->user->getSettingVal('security.regenerate.auth.key');
    }

    public function save() {
        $result = true;

        $setting_disableParallelSessions = UserSettings::findOne(['user_id' => $this->user_id, 'key' => 'security.disable.parallel.sessions']);
        if (!$setting_disableParallelSessions) {
            $setting_disableParallelSessions = new UserSettings();
            $setting_disableParallelSessions->user_id = $this->user_id;
        }
        $setting_disableParallelSessions->key = 'security.disable.parallel.sessions';
        $setting_disableParallelSessions->value = $this->disableParallelSessions;
        if (!$setting_disableParallelSessions->save()) {
            $result = false;
            $this->addError('regenerateAuthKey',  strip_tags(\yii\helpers\Html::errorSummary($setting_disableParallelSessions)));
        }

        $setting_regenerateAuthKey = UserSettings::findOne(['user_id' => $this->user_id, 'key' => 'security.regenerate.auth.key']);
        if (!$setting_regenerateAuthKey) {
            $setting_regenerateAuthKey = new UserSettings();
            $setting_regenerateAuthKey->user_id = $this->user_id;
        }
        $setting_regenerateAuthKey->key = 'security.regenerate.auth.key';
        $setting_regenerateAuthKey->value = $this->regenerateAuthKey;
        if (!$setting_regenerateAuthKey->save()) {
            $result = false;
            $this->addError('regenerateAuthKey',  strip_tags(\yii\helpers\Html::errorSummary($setting_regenerateAuthKey)));
        }

        return $result;
    }

    public function attributeLabels() {
        return [
            'disableParallelSessions' => Yii::t('app/user', 'Disable Parallel Sessions'),
            'regenerateAuthKey' => Yii::t('app/user', 'Regenerate Auth key after log out'),
        ];
    }

    /**
     * @return User
     */
    public function getUser() {
        if ($this->_user === null) {
            $this->_user = User::findOne($this->user_id);
        }
        return $this->_user;
    }

}
