<?php

namespace app\modules\info\models;

use Yii;

/**
 * This is the model class for table "{{%info_relation}}".
 *
 * @property integer $relation_id
 * @property integer $master_id
 * @property integer $slave_id
 * @property integer $type_id
 * @property integer $sort_order
 *
 * @property Info $master
 * @property Info $slave
 * @property InfoRelationType $type
 */
class Relation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%info_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['master_id', 'slave_id', 'type_id', 'sort_order'], 'integer'],
            [['master_id', 'slave_id'], 'exist', 'targetClass'=>Info::className(), 'targetAttribute'=>'info_id'],
            [['slave_id'], '_vClosure'],
            [['master_id', 'slave_id', 'type_id'], 'unique', 'targetAttribute' => ['master_id', 'slave_id', 'type_id'], 'message' => 'The combination of Master ID, Slave ID and Type ID has already been taken.']
        ];
    }

    public function _vClosure($attribute) {
        if ($this->slave_id==$this->master_id) {
            $this->addError($attribute,  Yii::t('info', 'The closure is unacceptable ({errno}).',['errno'=>0]));
        }
        foreach ($this->getMastersRelations() as $master) {
            if ($master->master_id==$this->slave_id) {
                $this->addError($attribute,  Yii::t('info', 'The closure is unacceptable ({errno}).',['errno'=>1]));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'relation_id' => Yii::t('info', 'Relation ID'),
            'master_id' => Yii::t('info', 'Master'),
            'slave_id' => Yii::t('info', 'Slave'),
            'type_id' => Yii::t('info', 'Type'),
            'sort_order' => Yii::t('info', 'Sort order'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaster()
    {
        return $this->hasOne(Info::className(), ['info_id' => 'master_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlave()
    {
        return $this->hasOne(Info::className(), ['info_id' => 'slave_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(InfoRelationType::className(), ['type_id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasterRelation()
    {
        return $this->hasOne(Relation::className(), ['type_id' => 'type_id', 'slave_id'=>'master_id']);
    }


    public function getMastersRelations($relation=null) {
        $res=[];
        $master=$relation?$relation->masterRelation:$this->masterRelation;
        if ($master) {
            $res[]=$master;
            $res=array_merge($res,$this->getMastersRelations($master));
        }
        return $res;
    }
}
