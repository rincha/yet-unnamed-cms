<?php

namespace app\modules\user\models;

use Yii;
use app\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_profile_person}}".
 *
 * @property integer $user_id
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string $birthday
 *
 * @property User $user
 */
class ProfilePerson extends \yii\db\ActiveRecord
{

    public static function getLabel() {
        return Yii::t('user/common','Person');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile_person}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required','on'=>'admin'],
            [['user_id'], 'integer','on'=>'admin'],
            [['user_id'], 'exist', 'targetClass'=>  User::className(),'targetAttribute'=>'id','on'=>'admin'],

            [['last_name', 'first_name'], 'required'],
            [['birthday'], 'date'],
            [['last_name', 'first_name', 'middle_name'], 'string', 'max' => 127]
        ];
    }

    public function behaviors()
    {
        return [
            'dateformat' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'birthday',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'birthday',
                ],
                'value' => function($event) {
                    return date('Y-m-d',  strtotime($event->sender->birthday));
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app/user', 'User'),
            'last_name' => Yii::t('user/common','Last Name'),
            'first_name' => Yii::t('user/common','First Name'),
            'middle_name' => Yii::t('user/common','Middle Name'),
            'birthday' => Yii::t('user/common','Birthday'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
