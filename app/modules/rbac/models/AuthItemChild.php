<?php

namespace app\modules\rbac\models;

use Yii;
use app\modules\rbac\models\AuthItem;

/**
 * This is the model class for table "{{%auth_item_child}}".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $parent0
 * @property AuthItem $child0
 */
class AuthItemChild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item_child}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
			[['parent', 'child'], 'exist', 'targetClass' => 'app\modules\rbac\models\AuthItem', 'targetAttribute'=>'name'],
			[['parent'], 'compare','compareAttribute'=>'child', 'operator'=>'!='],
			[['parent', 'child'], 'unique', 'targetAttribute' => ['parent', 'child']],
			[['child'], '_vClosure'],
        ];
    }
	
	//check parent is child of child
	public function _vClosure($attribute, $param) {
		$this->_vClosureC(
				AuthItemChild::find()->where(['child'=>$this->parent])->all(), 
				$this->child
		);
	}
	public function _vClosureC($parents, $first) {
		foreach ($parents as $parent) {
			if ($parent->parent==$first) {
				$this->addError('parent', 
						Yii::t('rbac', '({parent}) is a parent element ({this}) and can not be affiliated to it, check ({child})',
						['parent'=>$first,'child'=>$parent->child,'this'=>$this->parent]));
				return;
			}
			else {
				$this->_vClosureC(
					AuthItemChild::find()->where(['child'=>$parent->parent])->all(), 
					$first
				);
			}
		}
	}	

	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent' => Yii::t('rbac', 'Parent'),
            'child' => Yii::t('rbac', 'Child'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentItem()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildItem()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'child']);
    }
	
	
}
