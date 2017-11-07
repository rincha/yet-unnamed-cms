<?php

namespace app\modules\news\models;

use Yii;

/**
 * This is the model class for table "{{%news_type}}".
 *
 * @property integer $type_id
 * @property string $name
 * @property string $title
 *
 * @property News[] $news
 */
class NewsType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type_id' => Yii::t('news', 'ID'),
            'name' => Yii::t('news', 'Name'),
            'title' => Yii::t('news', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['type_id' => 'type_id']);
    }
}
