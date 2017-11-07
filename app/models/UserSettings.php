<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_settings}}".
 *
 * @property integer $user_id
 * @property string $key
 * @property string $value
 *
 * @property User $user
 */
class UserSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'key'], 'required'],
            [['user_id'], 'integer'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 64],
            [['user_id', 'key'], 'unique', 'targetAttribute' => ['user_id', 'key'], 'message' => Yii::t('app','The combination of User ID and Key has already been taken.')]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'ID'),
            'key' => Yii::t('app', 'Key'),
            'value' => Yii::t('app', 'Value'),
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
