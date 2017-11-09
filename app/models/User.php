<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use app\common\behaviors\ProfileBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use app\modules\rbac\models\AuthAssignment;
use app\modules\rbac\models\AuthItem;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password
 * @property string $new_password write-only
 * @property string $new_password_verify write-only
 * @property integer $status
 * @property string $auth_key
 * @property string $created_at
 * @property string $updated_at
 * @property string $verify_code write-only
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $authItems
 * @property UserAuthentication[] $authentications
 * @property UserLoginTry[] $loginTry
 * @property UserSettings[] $settings
 * //@property Profile[] $profile
 */
class User extends ActiveRecord implements IdentityInterface {

    const STATUS_NOACTIVE = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_LOCK = 20;
    const STATUS_DELETED = 30;

    public $password;
    public $new_password;
    public $new_password_verify;
    public $verify_code;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user}}';
    }

    public function scenarios() {
        $scenarios=[
            'default'=>[],
            'system'=>$this->attributes(),
            'register'=>['new_password', 'new_password_verify', 'verify_code',],
            'login'=>['username', 'password',],
            'login-captcha'=>['username', 'password','verify_code',],
            'password-change'=>['password', 'new_password','new_password_verify'],
            'password-reset'=>['new_password','new_password_verify'],
            'admin'=>['new_password','status','username',],
        ];
        if ((Yii::$app instanceof \yii\web\Application) && !Yii::$app->user->autoUsername) {
            $scenarios['register'][]='username';
        }
        return $scenarios;
    }

    /**
     * @inheritdoc
     * scenarios: register, login, login-captcha, password-change, admin
     */
    public function rules() {
        $rules=[
            [['username', 'password', 'new_password', 'new_password_verify', 'verify_code'], 'required', 'on'=>['register','password-change','password-reset']],
            [['password', 'new_password'], 'string', 'min' => 8, 'max' => 64],
            [['password', 'new_password'], 'match', 'pattern' => '/^[a-z0-9#_$@!.-]*$/i', 'message' => Yii::t('app/user','The password can contain only English letters, numbers and symbols: {symbols}',['symbols'=>'# _ $ @ ! . -'])],
        /**/[['password'], 'passwordCheck', 'on' => ['password-change']],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => [self::STATUS_NOACTIVE, self::STATUS_ACTIVE, self::STATUS_LOCK, self::STATUS_DELETED]],
            [['username'], 'string', 'min' => 3, 'max' => 64],
            [['username'], 'match', 'pattern' => '/^[a-z0-9_.-]*$/i', 'message' => Yii::t('app/user','Username must start with an english letter and can contain only english letters, numbers and symbols: {symbols}',['symbols'=>'_ . -'])],
        /**/[['username'], 'unique', 'on' => ['register', 'admin']],
            [['new_password_verify'], 'compare', 'compareAttribute' => 'new_password'],
            [['verify_code'], 'captcha', 'captchaAction' => 'site/captcha', 'on' => ['register', 'login-captcha']],
        ];
        return $rules;
    }

    public function passwordCheck($attribute) {
        if (!$this->validatePassword($this->{$attribute})) {
            $this->addError($attribute, Yii::t('app/user','The current password is incorrect.'));
        }
    }

    public function behaviors() {
        return [
            TimestampBehavior::className()=>[
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            ProfileBehavior::className()=>[
                'class'=>  ProfileBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app/user', 'ID'),
            'username' => Yii::t('app/user', 'Username'),
            'password' => Yii::t('app/user', 'Password'),
            'new_password' => Yii::t('app/user', 'Password'),
            'new_password_verify' => Yii::t('app/user', 'Password repeat'),
            'verify_code' => Yii::t('app/user', 'Verification code'),
            'status' => Yii::t('app/user', 'Status'),
            'statusName' => Yii::t('app/user', 'Status'),
            'auth_key' => Yii::t('app/user', 'Auth key'),
            'created_at' => Yii::t('app/user', 'Created time'),
            'updated_at' => Yii::t('app/user', 'Updated time'),
            '_authentication' => Yii::t('app/user', 'Authentication'),
            'authentications.email.uid'=>Yii::t('app/user', 'E-mail'),
        ];
    }

    public function attributeHints() {
        return [
            'username' => Yii::t('app/user', 'Username must start with an english letter and can contain only english letters, numbers and symbols: {symbols}',['symbols'=>'_ . -']),
            'password' => Yii::t('app/user','The password can contain only English letters, numbers and symbols: {symbols}',['symbols'=>'# _ $ @ ! . -']),
            'new_password' => Yii::t('app/user','The password can contain only English letters, numbers and symbols: {symbols}',['symbols'=>'# _ $ @ ! . -']),
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if (
                    $this->new_password &&
                    ($this->isNewRecord ||
                    ($this->scenario == 'admin' && $this->new_password != $this->getOldAttribute('new_password')) ||
                    $this->scenario == 'system' ||
                    $this->scenario == 'password-reset' ||
                    $this->scenario == 'password-change')
            ) {
                $this->setNewPassword($this->new_password);
            }
            if ($this->isNewRecord) {
                $this->generateAuthKey();
            }
            return true;
        } else
            return false;
    }

    public static function getStatusesList() {
        return [
            self::STATUS_NOACTIVE => \Yii::t('app/user', 'inactive'),
            self::STATUS_ACTIVE => \Yii::t('app/user', 'active'),
            self::STATUS_LOCK => \Yii::t('app/user', 'locked'),
            self::STATUS_DELETED => \Yii::t('app/user', 'deleted'),
        ];
    }

    public function getStatusName() {
        if (isset($this->statusesList[$this->status])) {
            return $this->statusesList[$this->status];
        } else
            return null;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setNewPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates password
     */
    public function generatePassword() {
        $this->new_password = Yii::$app->security->generateRandomString(12);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString(32);
    }

    /**
     * Generates "username"
     */
    public function getUid() {
        return $this->username?$this->username:str_pad($this->id, 10, '0', STR_PAD_LEFT);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments() {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItems() {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])
                ->viaTable(AuthAssignment::tableName(), ['user_id' => 'id'])
                ->indexBy('name');
    }

    public static function findByUsername($id) {
        if (Yii::$app->user->autoUsername) {
            return self::findIdentity($id);
        }
        else {
            return static::findOne(['username' => $id, 'status' => self::STATUS_ACTIVE]);
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthentications() {
        return $this->hasMany(UserAuthentication::className(), ['user_id' => 'id'])->indexBy('type');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettings() {
        return $this->hasMany(UserSettings::className(), ['user_id' => 'id'])->indexBy('key');
    }

    /**
     * @return mixed
     */
    public function getSettingVal($key) {
        return isset($this->settings[$key])?$this->settings[$key]->value:null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getProfile() {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoginTry() {
        return $this->hasOne(UserLoginTry::className(), ['user_id' => 'id']);
    }


    public function addLoginTry() {
        $this->refresh();
        if (!$this->loginTry) {
            $try=new UserLoginTry();
            $try->user_id=$this->id;
            $try->count=1;
            return $try->save();
        }
        else {
            $this->loginTry->count++;
            return $this->loginTry->save();
        }
    }
    public function clearLoginTry() {
        $this->refresh();
        if (!$this->loginTry) {
            $try=new UserLoginTry();
            $try->user_id=$this->id;
            $try->count=0;
            return $try->save();
        }
        else {
            $this->loginTry->count=0;
            return $this->loginTry->save();
        }
    }

}
