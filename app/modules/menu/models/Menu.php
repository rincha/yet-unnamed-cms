<?php

namespace app\modules\menu\models;

use Yii;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property integer $menu_id
 * @property string $key
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $options
 *
 * @property MenuItem[] $menuItems
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['description'], 'string'],
            [['key', 'name', 'type'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['options'], 'string', 'max' => 1024],
            [['key'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => Yii::t('menu','ID'),
            'key' => Yii::t('menu','Key'),
            'name' => Yii::t('menu','Name'),
            'title' => Yii::t('menu','Title'),
            'description' => Yii::t('menu','Description'),
            'type' => Yii::t('menu','Type'),
            'options' => Yii::t('menu','Options'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItems()
    {
        return $this->hasMany(MenuItem::className(), ['menu_id' => 'menu_id']);
    }
	
	/**
     * @return MenuItem[]
     */
    public function getItemsLevel($parent_id=null) {
		return MenuItem::find()
				->select(['i.*', 'IF(ic.menu_item_id IS NULL,0,1) as has_children'])
				->from(MenuItem::tableName().' i')
				->leftJoin(MenuItem::tableName().' ic', 'ic.parent_id=i.menu_item_id')
				->where(['i.menu_id'=>$this->menu_id,'i.parent_id'=>$parent_id])
				->groupBy('i.menu_item_id')
				->orderBy('i.sort_order,i.name')
				->indexBy('menu_item_id')
				->all();
		
    }
	
	/**
     * @return MenuItem[]
     */
    public function getItemsList($parent_id=null,$depth=0) {
		$res=[];
		$models=MenuItem::find()
				->select(['i.*', 'IF(ic.menu_item_id IS NULL,0,1) as has_children'])
				->from(MenuItem::tableName().' i')
				->leftJoin(MenuItem::tableName().' ic', 'ic.parent_id=i.menu_item_id')
				->where(['i.menu_id'=>$this->menu_id,'i.parent_id'=>$parent_id])
				->groupBy('i.menu_item_id')
				->orderBy('i.sort_order,i.name')
				->indexBy('menu_item_id')
				->all();
		foreach ($models as $model) {
			$res[$model->menu_item_id]=str_pad('', $depth, '-', STR_PAD_LEFT).$model->name;
			if ($model->has_children) $res+=$this->getItemsList($model->menu_item_id,$depth+1);
		}
		return $res;
    }
	
	/**
     * @return MenuItem[]
     */
    public function getItemsAll($parent_id=null,$depth=0) {
		$res=[];
		$models=MenuItem::find()
				->select(['i.*', 'IF(ic.menu_item_id IS NULL,0,1) as has_children'])
				->from(MenuItem::tableName().' i')
				->leftJoin(MenuItem::tableName().' ic', 'ic.parent_id=i.menu_item_id')
				->where(['i.menu_id'=>$this->menu_id,'i.parent_id'=>$parent_id])
				->groupBy('i.menu_item_id')
				->orderBy('i.sort_order,i.name')
				->indexBy('menu_item_id')
				->all();
		foreach ($models as $model) {
			$model->level=$depth;
			$res[$model->menu_item_id]=$model;
			if ($model->has_children) $res+=$this->getItemsAll($model->menu_item_id,$depth+1);
		}
		return $res;
    }
	
}
