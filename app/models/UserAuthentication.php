<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%account}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $uid
 * @property string $verification
 * @property string $verification_expire
 * @property integer $status
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 * @property string $verify_code write-only
 *
 * @property User $user
 * @property authentication\BaseType $typeModel
 */
class UserAuthentication extends \yii\db\ActiveRecord {

    const STATUS_NOACTIVE = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_LOCK = 20;
    const STATUS_DRAFT = 30;

    private $_type_model = null;
    public $verify_code;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user_authentication}}';
    }

    public function scenarios() {
        return [
            'default'=>[],
            'system'=>$this->attributes(),
            'admin'=>['type','uid','status'],
            'register'=>['type', 'uid'],
            'add'=>['type', 'uid'],
            'acitivate'=>['type', 'uid', 'verify_code'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'uid'], 'required'],
            [['type'], 'string', 'max' => 64],
            [['type'], '_vType'],
            [['uid'], 'string', 'max' => 128],
            [['uid'], 'filter', 'filter' => 'trim'],
            [['uid'], '_vTypeModel'],
            [['type'], 'unique', 'targetAttribute' => ['user_id', 'type'], 'on'=>['add','register','admin']],
            [['uid'], 'unique', 'targetAttribute' => ['type', 'uid'], 'on'=>['add','register','admin']],
            [['verify_code'], 'captcha', 'captchaAction' => 'site/captcha'],
            [['status'], 'in', 'range'=>  array_keys($this->getStatusesList())],
        ];
    }

    public function _vType($attribute) {
        if (!$this->hasErrors('type') && !$this->isTypeExist()) {
            $this->addError($attribute, 'Данное значение ' . $this->getAttributeLabel('type') . ' отсутствует в списке.');
        }
    }

    public function _vTypeModel() {
        if (!$this->hasErrors('type') && in_array('validate', $this->typeModel->allowedMethods())) {
            $vr = $this->getTypeModel(true)->validate();
            if ($vr !== true) {
                //var_dump($vr); die();
                $this->addErrors($vr);
            }
        }
    }

    /**
     * @return \app\models\authentication\BaseType
     */
    public function getTypeModel($refresh = false) {
        if ($refresh)
            $this->_type_model = null;

        if (!$this->_type_model && $this->type && $this->isTypeExist()) {
            $class_name = '\app\models\authentication\\' . ucfirst($this->type);
            $this->_type_model = new $class_name($this);
        }

        return $this->_type_model;
    }

    public function getTypeName() {
        return Yii::$app->user->authentications['types'][$this->type]['name'];
    }

    public function getTypeConfig() {
        return \yii\helpers\ArrayHelper::getValue(Yii::$app->user->authentications['types'][$this->type],'config');
    }

    public function getTypeParams() {
        return \yii\helpers\ArrayHelper::getValue(Yii::$app->user->authentications['types'],$this->type);
    }

    private function isTypeExist() {
        return isset(Yii::$app->user->authentications['types'][$this->type]);
    }

    public function behaviors() {
        return [
            TimestampBehavior::className()=>[
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public static function getTypesList() {
        $res=[];
        foreach (Yii::$app->user->authentications['types'] as $k=>$v) {
            $res[$k]=$v['name'];
        }
        return $res;
    }

    public static function getStatusesList() {
        return [
            self::STATUS_NOACTIVE => \Yii::t('app/user', 'inactive'),
            self::STATUS_ACTIVE => \Yii::t('app/user', 'active'),
            self::STATUS_LOCK => \Yii::t('app/user', 'locked'),
            self::STATUS_DRAFT => \Yii::t('app/user', 'draft'),
        ];
    }

    public function getStatusName() {
        if (isset($this->statusesList[$this->status])) {
            return $this->statusesList[$this->status];
        } else
            return null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'account_id' => Yii::t('app/user', 'Account ID'),
            'user_id' => Yii::t('app/user', 'User ID'),
            'type' => Yii::t('app/user', 'Type'),
            'typeName' => Yii::t('app/user', 'Type'),
            'uid' => $this->typeParams?$this->typeParams['name']:Yii::t('app/user', 'Uid'),
            'verification' => Yii::t('app/user', 'Activation code'),
            'verification_expire' => Yii::t('app/user', 'Activation code expire'),
            'status' => Yii::t('app/user', 'Status'),
            'statusName' => Yii::t('app/user', 'Status'),
            'created_at' => Yii::t('app/user', 'Created time'),
            'updated_at' => Yii::t('app/user', 'Updated time'),
            'verify_code' => Yii::t('app/user', 'Verification code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
