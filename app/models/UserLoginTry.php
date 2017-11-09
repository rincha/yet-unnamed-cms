<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%user_login_try}}".
 *
 * @property integer $user_id
 * @property integer $count
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserLoginTry extends \yii\db\ActiveRecord {

    const TRY_INTERVAL=900;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user_login_try}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [];
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'user_id' => 'User ID',
            'count' => 'Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
