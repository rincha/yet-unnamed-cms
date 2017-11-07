<?php

namespace app\modules\info\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\common\behaviors\DateConvertBehavior;
use yii\helpers\ArrayHelper;
use app\common\helpers\AppHelper;

/**
 * This is the model class for table "{{%info}}".
 *
 * @property integer $info_id
 * @property integer $type_id
 * @property string $uid
 * @property string $name
 * @property string $h1
 * @property string $meta_title
 * @property string $meta_description
 * @property string $keywords
 * @property string $content
 * @property string $images
 * @property string $params
 * @property string $date
 * @property string $created_at
 * @property string $updated_at
 *
 *
 * @property Type $type
 * @property Relation[] $masters
 * @property Relation[] $slaves
 */
class Info extends \yii\db\ActiveRecord {

    public $safe=1;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type_id'], 'integer'],
            [['name'], 'required'],
            [['images'], '_vImages'],
            [['content', 'images', 'params'], 'string', 'max' => 1024 * 128],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['uid'], 'string', 'max' => 64],
            [['name', 'h1', 'meta_title'], 'string', 'max' => 255],
            [['meta_description', 'keywords'], 'string', 'max' => 1024],
            [['uid'], 'default', 'value' => null],
            [['uid'], 'unique'],
            [['content'],'_vHtml'],
            [['content'], 'string', 'max' => 1024 * 128],
        ];
    }

    public function _vHtml() {
        if ($this->safe) {
            $this->content=AppHelper::htmlPurifyFull($this->content);
        }
    }

    public function _vImages() {
        if (is_array($this->images)) {
            $this->images = implode("\n", $this->images);
        }
    }

    public function behaviors() {
        return [
            TimestampBehavior::className() => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            DateConvertBehavior::className() => [
                'class' => DateConvertBehavior::className(),
                'attributes' => ['date'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'info_id' => Yii::t('info', 'ID'),
            'type_id' => Yii::t('info', 'Type'),
            'type.name' => Yii::t('news', 'Type'),
            'typeName' => Yii::t('news', 'Type'),
            'uid' => Yii::t('info', 'Uid'),
            'name' => Yii::t('info', 'Name'),
            'h1' => Yii::t('info', 'Header H1'),
            'meta_title' => Yii::t('info', 'Meta Title'),
            'meta_description' => Yii::t('info', 'Meta Description'),
            'keywords' => Yii::t('info', 'Keywords'),
            'content' => Yii::t('info', 'Content'),
            'images' => Yii::t('info', 'Images'),
            'params' => Yii::t('info', 'Params'),
            'date' => Yii::t('info', 'Date'),
            'created_at' => Yii::t('info', 'Created At'),
            'updated_at' => Yii::t('info', 'Updated At'),
            'safe' => Yii::t('info', 'Filter HTML'),
        ];
    }

    public function getImage($index = 0) {
        return ArrayHelper::getValue($this->images, $index);
    }

    public function getTypeName() {
        return $this->type ? $this->type->name : null;
    }

    public function getUrlArr() {
        if ($this->uid) {
            return ['/info/default/view', 'id' => $this->uid];
        } else {
            return ['/info/default/view', 'id' => $this->info_id];
        }
    }

    public function getUrl($schema = false) {
        return \yii\helpers\Url::to($this->getUrlArr(), $schema);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType() {
        return $this->hasOne(Type::className(), ['type_id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasters() {
        return $this->hasMany(Relation::className(), ['master_id' => 'info_id']);
    }

    private $_masters_by_type;
    /**
     * @return Relation[]
     */
    public function getMastersByType($type_id) {
        if ($this->_masters_by_type===null || !isset($this->_masters_by_type[$type_id])) {
            $this->_masters_by_type[$type_id]=Relation::find()->where(['slave_id' => $this->info_id, 'type_id' => $type_id])->all();
        }
        return $this->_masters_by_type[$type_id];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlaves() {
        return $this->hasMany(Relation::className(), ['slave_id' => 'info_id']);
    }

    private $_slaves_by_type;
    /**
     * @return Relation[]
     */
    public function getSlavesByType($type_id) {
        if ($this->_slaves_by_type===null || !isset($this->_slaves_by_type[$type_id])) {
            $this->_slaves_by_type[$type_id]=$this->getSlavesByTypeQuery($type_id)->all();
        }
        return $this->_slaves_by_type[$type_id];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlavesByTypeQuery($type_id) {
        return Relation::find()
                ->with(['slave'])
                ->where([
                    'master_id' => $this->info_id,
                    'type_id' => $type_id]
                )
                ->orderBy(['sort_order'=>SORT_ASC])
                ->indexBy('relation_id');
    }

}
