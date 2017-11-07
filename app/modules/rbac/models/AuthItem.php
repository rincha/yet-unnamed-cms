<?php

namespace app\modules\rbac\models;

use Yii;
use yii\rbac\Item;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property User[] $users
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 */
class AuthItem extends ActiveRecord {

    public $new_children;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'type'], 'required'],
            [['name'], 'unique'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['type'], 'integer'],
            [['type'], 'in', 'range' => [Item::TYPE_ROLE, Item::TYPE_PERMISSION]],
            [['rule_name'], 'default', 'value' => null],
            [['rule_name'], 'exist', 'targetClass' => '\app\modules\rbac\models\AuthRule', 'targetAttribute' => 'name'],
            ['new_children', 'safe'],
        ];
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'name' => Yii::t('rbac', 'Name'),
            'type' => Yii::t('rbac', 'Type'),
            'description' => Yii::t('rbac', 'Description'),
            'rule_name' => Yii::t('rbac', 'Rule Name'),
            'data' => Yii::t('rbac', 'Data'),
            'created_at' => Yii::t('rbac', 'Created At'),
            'updated_at' => Yii::t('rbac', 'Updated At'),
            'authItemChildren' => Yii::t('rbac', 'Auth item children'),
            'new_children' => Yii::t('rbac', 'Add children'),
        ];
    }

    public static function getTypesLabels() {
        return [Item::TYPE_ROLE => Yii::t('rbac', 'Role'), Item::TYPE_PERMISSION => Yii::t('rbac', 'Permission')];
    }

    public function getTypeLabel() {
        if (isset($this->typesLabels[$this->type]))
            return $this->typesLabels[$this->type];
        else
            return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments() {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%auth_assignment}}', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName() {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren() {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

}
