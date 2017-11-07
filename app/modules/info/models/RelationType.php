<?php

namespace app\modules\info\models;

use Yii;

/**
 * This is the model class for table "{{%info_relation_type}}".
 *
 * @property integer $type_id
 * @property string $name
 * @property string $title
 *
 * @property InfoRelation[] $infoRelations
 */
class RelationType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%info_relation_type}}';
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
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type_id' => Yii::t('info', 'Type ID'),
            'name' => Yii::t('info', 'Unique name'),
            'title' => Yii::t('info', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfoRelations()
    {
        return $this->hasMany(InfoRelation::className(), ['type_id' => 'type_id']);
    }
}
