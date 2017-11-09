<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\components;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\UserEvent;

/**
 *
 * @author rincha
 */
class User extends \yii\web\User {

    const EVENT_AFTER_RENEW_AUTH_STATUS = 'afterRenewAuthStatus';

    public $rememberCookieLifetime = 0;
    public $autoUsername = false;
    public $authentications = [
        'max_login_try' => 3,
        'enabled' => true,
        'required' => true,
        'types' => [
            'email' => [
                'id' => 'email',
                'name' => 'Email',
                'enabled' => true,
                'required' => true,
                'activation' => true,
                'iconClass' => 'fa fa-envelope',
                'loginUidPatterns' => [
                    '/.+@.+/ui' => 'Email',
                ],
            ],
        ]
    ];
    public $authenticationsUidPatterns = [
        '/.+@.+/ui' => 'email',
    ];
    private $_profiles = [];
    public $profilesRequired = [];

    /**
     *
     * id - ID of profile
     * property - property for relation (see profile behavior)
     * search - false or [ 'attributes'=>[attributes], 'dp'=>function ($userSearchModel,$dataProvider,$query) {return ['dataProvider'=>ActiveDataProvider, 'query'=>ActiveQuery];}]
     * class - profile class
     * behavior - profile behavior
     */
    public function setProfiles(Array $value) {
        Yii::trace('setProfiles');
        if (is_array($value)) {
            foreach ($value as $v) {
                if (is_string($v)) {
                    $this->_profiles[$v] = [
                        'id' => $v,
                        'property' => 'profile' . ucfirst($v),
                        'search' => false,
                        'class' => 'app\modules\user\models\Profile' . ucfirst($v),
                        'behavior' => 'app\modules\user\behaviors\Profile' . ucfirst($v) . 'Behavior',
                    ];
                } elseif (is_array($v) && isset($v['id'])) {
                    $this->_profiles[$v['id']] = [
                        'id' => $v['id'],
                        'property' => ArrayHelper::getValue($v, 'property', 'profile' . ucfirst($v['id'])),
                        'search' => ArrayHelper::getValue($v, 'search', false),
                        'class' => ArrayHelper::getValue($v, 'class', 'app\modules\user\models\Profile' . ucfirst($v['id'])),
                        'behavior' => ArrayHelper::getValue($v, 'behavior', 'app\modules\user\behaviors\Profile' . ucfirst($v['id']) . 'Behavior'),
                    ];
                } else {
                    throw new Exception('User profile must be Array with id or string');
                }
            }
        } else {
            throw new Exception('User profiles must be Array');
        }
    }

    public function getProfiles() {
        return $this->_profiles;
    }

    protected function beforeLogin($identity, $cookieBased, $duration) {
        Yii::trace('Before Login');
        if ($identity->getSettingVal('security.disable.parallel.sessions')) {
            Yii::$app->session->destroyUserSessions($identity->id);
            $identity->generateAuthKey();
            $identity->save();
            Yii::trace('Destroy other user sessions.');
        }
        return parent::beforeLogin($identity, $cookieBased, $duration);
    }

    protected function afterLogout($identity) {
        Yii::trace('After Logout');
        if ($identity->getSettingVal('security.regenerate.auth.key')) {
            Yii::$app->session->destroyUserSessions($identity->id);
            $identity->generateAuthKey();
            $identity->save();
            Yii::trace('Destroy other user sessions, regenerate auth key.');
        }
        return parent::afterLogout($identity);
    }

    public function renewAuthStatus() {
        parent::renewAuthStatus();
        $this->afterRenewAuthStatus($this->getIdentity(false));
    }

    /**
     * @param IdentityInterface $identity the user identity information
     */
    protected function afterRenewAuthStatus($identity) {
        $this->trigger(self::EVENT_AFTER_RENEW_AUTH_STATUS, new UserEvent([
            'identity' => $identity,
        ]));
    }

}
