<?php

namespace app\modules\banner\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%banner}}".
 *
 * @property integer $banner_id
 * @property integer $type_id
 * @property string $name
 * @property string $title
 * @property string $text
 * @property string $data
 * @property string $start_at
 * @property string $end_at
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BannerItem[] $items
 */
class Banner extends \yii\db\ActiveRecord {

    const TYPE_IMAGE=0;
    const TYPE_SLIDER=1;

    const STATUS_ENABLED=1;
    const STATUS_DISABLED=0;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%banner}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type_id', 'name', 'status'], 'required'],
            [['type_id', 'status'], 'integer'],
            [['text', 'data'], 'string', 'max'=>1024*32],
            [['start_at', 'end_at'], 'date', 'format'=>'php:Y-m-d'],
            [['end_at'],  'compare', 'operator'=>'>=','compareAttribute'=>'start_at'],
            [['name'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['type_id'],'in','range'=> array_keys(self::getTypeList())],
            [['status'],'in','range'=> array_keys(self::getStatusList())],
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className() => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public static function getTypeList() {
        return [
            self::TYPE_IMAGE=>'Изображение',
            self::TYPE_SLIDER=>'Слайдер',
        ];
    }

    public static function getStatusList() {
        return [
            self::STATUS_DISABLED=>'Отключен',
            self::STATUS_ENABLED=>'Включен',
        ];
    }

    public function getStatusText() {
        return ArrayHelper::getValue(self::getStatusList(), $this->status);
    }

    public function getTypeText() {
        return ArrayHelper::getValue(self::getTypeList(), $this->type_id);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'banner_id' => 'ID',
            'type_id' => 'Тип',
            'typeText' => 'Тип',
            'name' => 'Название',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'data' => 'Данные',
            'start_at' => 'Начало показа',
            'end_at' => 'Окончание показа',
            'status' => 'Статус',
            'statusText' => 'Статус',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems() {
        return $this->hasMany(BannerItem::className(), ['banner_id' => 'banner_id']);
    }

}
