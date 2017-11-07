<?php

namespace app\modules\banner\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%banner_item}}".
 *
 * @property integer $item_id
 * @property integer $banner_id
 * @property integer $sort
 * @property string $image
 * @property string $link
 * @property string $title
 * @property string $text
 * @property string $data
 * @property string $start_at
 * @property string $end_at
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Banner $banner
 */
class BannerItem extends \yii\db\ActiveRecord {

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%banner_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['banner_id', 'status'], 'required'],
            [['banner_id', 'sort', 'status'], 'integer'],
            [['text', 'data'], 'string', 'max'=>1024*32],
            [['start_at', 'end_at'],  'date', 'format'=>'php:Y-m-d'],
            [['end_at'],  'compare', 'operator'=>'>=','compareAttribute'=>'start_at'],
            [['image', 'link', 'title'], 'string', 'max' => 255],
            [['banner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Banner::className(), 'targetAttribute' => ['banner_id' => 'banner_id']],
            [['status'],'in','range'=> array_keys(self::getStatusList())],
            [['link'], 'url'],
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

    public static function getStatusList() {
        return [
            self::STATUS_DISABLED=>'Отключен',
            self::STATUS_ENABLED=>'Включен',
        ];
    }

    public function getStatusText() {
        return ArrayHelper::getValue(self::getStatusList(), $this->status);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'item_id' => 'ID',
            'banner_id' => 'Баннер',
            'sort' => 'Порядок сортировки',
            'image' => 'Изображение',
            'link' => 'Ссылка',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'data' => 'Данные',
            'start_at' => 'Дата начала',
            'end_at' => 'Дата окончания',
            'status' => 'Статус',
            'statusText' => 'Статус',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanner() {
        return $this->hasOne(Banner::className(), ['banner_id' => 'banner_id']);
    }

}
