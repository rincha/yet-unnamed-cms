<?php

namespace app\modules\rbac\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\rbac\models\AuthItem;
use app\modules\user\models\User;

/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $item_name
 * @property integer $user_id
 * @property integer $created_at
 *
 * @property User $user
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%auth_assignment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['item_name', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['item_name'], 'string', 'max' => 64],
            [['item_name'], 'exist', 'targetClass' => 'app\modules\rbac\models\AuthItem', 'targetAttribute' => 'name'],
            [['user_id'], 'exist', 'targetClass' => 'app\models\User', 'targetAttribute' => 'id'],
            [['item_name', 'user_id'], 'unique', 'targetAttribute' => ['item_name', 'user_id']],
        ];
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'item_name' => Yii::t('rbac', 'Permission'),
            'user_id' => Yii::t('rbac', 'User'),
            'created_at' => Yii::t('rbac', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItem() {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }

}
