<?php

namespace app\modules\promo\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%promo}}".
 *
 * @property integer $promo_id
 * @property string $uid
 * @property string $name
 * @property string $meta_title
 * @property string $meta_description
 * @property string $keywords
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PromoBlock[] $blocks
 *
 * @property string $statusName
 */
class Promo extends \yii\db\ActiveRecord
{
    const STATUS_DISABLED=0;
    const STATUS_ENABLED=1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promo}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['uid'], 'string', 'max' => 64],
            [['uid'], 'match', 'pattern' => '/^[a-zA-Zа-яА-Я]+[a-zA-Z0-9а-яА-Я-]*$/ui'],
            [['name', 'meta_title'], 'string', 'max' => 255],
            [['meta_description', 'keywords'], 'string', 'max' => 1024],
            [['uid'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'in', 'range'=>  array_keys($this->statusList())],
        ];
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'promo_id' => Yii::t('promo', 'Promo ID'),
            'uid' => Yii::t('promo', 'Uid'),
            'name' => Yii::t('promo', 'Name'),
            'meta_title' => Yii::t('promo', 'Meta title'),
            'meta_description' => Yii::t('promo', 'Meta description'),
            'keywords' => Yii::t('promo', 'Keywords'),
            'status' => Yii::t('promo', 'Status'),
            'statusName' => Yii::t('promo', 'Status'),
            'created_at' => Yii::t('promo', 'Created at'),
            'updated_at' => Yii::t('promo', 'Updated at'),
        ];
    }

    /**
     * @return Array
     */
    public function statusList() {
        return [
            self::STATUS_DISABLED=>  Yii::t('promo', 'disabled'),
            self::STATUS_ENABLED=>  Yii::t('promo', 'enabled'),
        ];
    }
    /**
     * @return string
     */
    public function getStatusName() {
        return ArrayHelper::getValue($this->statusList(), $this->status);
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlocks()
    {
        return $this->hasMany(PromoBlock::className(), ['promo_id' => 'promo_id'])->orderBy(['sort_order'=>SORT_ASC,'name'=>SORT_ASC]);
    }
}
