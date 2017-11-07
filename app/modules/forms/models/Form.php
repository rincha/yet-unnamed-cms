<?php

namespace app\modules\forms\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%form}}".
 *
 * @property integer $form_id
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $button
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FormField[] $fields
 */
class Form extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%form}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['description'], 'string', 'max' => 64 * 1024],
            [['name', 'type'], 'string', 'max' => 64],
            [['type'], 'in', 'range' => array_keys(self::getTypes())],
            [['title','button'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['emails'], 'string', 'max' => 512],
            [['phone'], 'string', 'max' => 11, 'min'=>11],
            [['status'], 'boolean'],
        ];
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

    public static function getTypes() {
        return [
            'default' => Yii::t('app','Default'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'form_id' => 'ID',
            'name' => Yii::t('forms','Unique name'),
            'title' => Yii::t('forms','Title'),
            'description' => Yii::t('forms','Description'),
            'button' => Yii::t('forms','Button'),
            'type' => Yii::t('forms','Type'),
            'created_at' => Yii::t('forms','Created at'),
            'updated_at' => Yii::t('forms','Updated at'),
            'emails' => Yii::t('forms','Recipient Email-addresses (separated by commas)'),
            'phone' => Yii::t('forms','Recipient`s phone for SMS'),
            'status' => Yii::t('forms','Available'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFields() {
        return $this->hasMany(FormField::className(), ['form_id' => 'form_id']);
    }

    public function getEmailList() {
        $list=str_replace(',',  ' ', $this->emails);
        $list=preg_replace('/  /', ' ', $list);
        $list=explode(' ', $list);
        $res=[];
        foreach ($list as $email) {
            $validator=new \yii\validators\EmailValidator(['allowName'=>false]);
            if ($validator->validate($email)) $res[]=$email;
        }
        return $res;
    }

    public function getUrlArr() {
        return ['/forms/default/view','id'=>$this->name?$this->name:$this->form_id];
    }

    public function getUrl() {
        return \yii\helpers\Url::to($this->getUrlArr());
    }

}
