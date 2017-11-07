<?php

namespace app\modules\info\models;

use Yii;

/**
 * This is the model class for table "{{%info_type}}".
 *
 * @property integer $type_id
 * @property string $name
 * @property string $title
 *
 * @property Info[] $infos
 */
class Type extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%info_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'match', 'pattern'=>'/^[a-z0-9_-]*$/ui'],
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
            'type_id' => Yii::t('info', 'ID'),
            'name' => Yii::t('info', 'Type'),
            'title' => Yii::t('info', 'Title'),
        ];
    }

    public function getUrlArr() {
        return ['/info/default/index','type'=>$this->name?$this->name:$this->type_id];
    }

    public function getUrl() {
        return \yii\helpers\Url::to($this->getUrlArr());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfos()
    {
        return $this->hasMany(Info::className(), ['type_id' => 'type_id']);
    }

}
