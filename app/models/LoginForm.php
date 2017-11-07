<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 *
 * @author rincha
 *
 * @property app\models\User $user
 */
class LoginForm extends \yii\base\Model
{
    private $_user=null;

    public $username;
    public $password;
    public $verify_code;

    public function scenarios() {
        return [
            'login'=>['username', 'password',],
            'login-captcha'=>['username', 'password','verify_code',],
        ];
    }

    public function rules()
    {
        return [
            [['username', 'password', 'verify_code'], 'required'],
            [['username', 'password'], 'filter', 'filter'=>'trim'],
            [['verify_code'], 'captcha', 'captchaAction' => 'site/captcha'],
            [['password','username'], 'string', 'max' => 64],
            [['username'], '_vExists'],
            [['password'], '_vPassword'],
        ];
    }

    public function _vExists() {
        if (!$this->hasErrors()) {
            $user=$this->findUser();
            if (!$user) {
                $this->addError('username',  Yii::t('app/user', 'User {username} not found.',['username'=>$this->username]));
            }
            elseif ($user->status !== User::STATUS_ACTIVE) {
                if ($user->status === User::STATUS_NOACTIVE) {
                    $this->addError('username', Yii::t('app/user', 'Account not activated.'));
                }
                else {
                    $this->addError('username', Yii::t('app/user', 'Login can not be completed when the status of the account {status}, refer to the administration.', ['status' => $user->statusName]));
                }
            }
        }
    }

    public function _vPassword() {
        if (!$this->hasErrors()) {
            if (!$this->user->password_hash || !$this->user->validatePassword($this->password)) {
                $this->addError('username',  Yii::t('app/user', 'Login or password is incorrect.'));
            }
        }
    }

    //TODO remove this



    /**
     * @return User
     */
    public function getUser($force=false) {
        if ($this->_user!==null && !$force) {
            return $this->_user;
        }
        else {
            $this->_user=$this->findUser();
            return $this->_user;
        }
    }

    /**
     * @return User
     */
    private function findUser() {
        $user=$this->findUserByUsername();
        if (!$user) {
            $user=$this->findUserByAuthUid();
        }
        return $user;
    }

    /**
     * @return User
     */
    private function findUserByUsername() {
        return User::findOne(['username' => $this->username]);
    }

    /**
     * @return User
     */
    private function findUserByAuthUid() {
        foreach ($this->getAuthPatterns() as $pattern) {
            if (preg_match($pattern['pattern'], $this->username)===1) {
                $auth=UserAuthentication::findOne(['type'=>$pattern['type']['id'],'uid'=>$this->username]);
                if ($auth) {
                    return $auth->user;
                }
            }
        }
        return null;
    }

    private function getAuthPatterns() {
        $res=[];
        if (Yii::$app->user->authentications['types']) {
            foreach (Yii::$app->user->authentications['types'] as $type) {
                foreach (ArrayHelper::getValue($type, 'loginUidPatterns',[]) as $pattern=>$name) {
                    $res[]=[
                        'type'=>$type,
                        'pattern'=>$pattern,
                        'name'=>$name
                    ];
                }
            }
        }
        return $res;
    }

    public function attributeLabels() {
        return [
            'username' => Yii::t('app/user', 'Username'),
            'password' => Yii::t('app/user', 'Password'),
            'verify_code' => Yii::t('app/user', 'Verification code'),
        ];
    }

    public function attributeHints() {
        $username=[Yii::t('app/user', 'Username')];
        foreach ($this->getAuthPatterns() as $pattern) {
            if ($pattern['name']) {
                $username[]=$pattern['name'];
            }
        }
        return [
            'username' => implode(', ', $username),
        ];
    }

}