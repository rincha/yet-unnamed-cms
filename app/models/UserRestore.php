<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_restore}}".
 *
 * @property string $type
 * @property string $uid
 * @property string $reset_token
 * @property string $expire
 * @property string $verify_code
 *
 * @property UserAuthentication $authentication
 */
class UserRestore extends \yii\db\ActiveRecord
{

    public $verify_code;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_restore}}';
    }

    public function scenarios() {
        return [
            'new'=>['type','uid','verify_code'],
            'send'=>['type','uid'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'uid'], 'required'],
            [['type'], 'string', 'max' => 64],
            [['uid'], 'string', 'max' => 128],

            ['verify_code', 'captcha', 'captchaAction' => 'site/captcha','on'=>'new'],
            [['uid'], '_vAuth'],
        ];
    }

    public function _vAuth() {
        if (!$this->hasErrors('type') && !$this->hasErrors('uid') && !$this->hasErrors('verify_code')) {
            $model=  UserAuthentication::findOne(['uid'=>$this->uid, 'type'=>$this->type]);
            if (!$model) {
                $this->addError('uid', Yii::t('app/user','User with {type}:{uid} not found.',['uid'=>$this->uid, 'type'=>$this->type]));
            }
            elseif ($model->status != UserAuthentication::STATUS_ACTIVE || $model->user->status!=User::STATUS_ACTIVE) {
                $this->addError('uid', Yii::t('app/user','User account {type}:{uid} is not active.',['uid'=>$this->uid, 'type'=>$this->type]));
            }
            elseif (!in_array ('sendRestore', $model->typeModel->allowedMethods())) {
                $this->addError('type', Yii::t('app/user','Account {type} can not be used to restore access.',['type'=>$this->type]));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('app/user', 'Type'),
            'uid' => Yii::t('app/user', 'Uid'),
            'reset_token' => Yii::t('app/user', 'Reset token'),
            'expire' => Yii::t('app/user', 'Expire'),
            'verify_code' => Yii::t('app/user', 'Verification code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthentication()
    {
        return $this->hasOne(UserAuthentication::className(), ['type' => 'type', 'uid' => 'uid']);
    }
}
