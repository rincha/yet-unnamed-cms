<?php

namespace app\modules\menu\models;

use Yii;
use app\modules\menu\models\Menu;

/**
 * This is the model class for table "{{%menu_item}}".
 *
 * @property integer $menu_item_id
 * @property integer $menu_id
 * @property integer $parent_id
 * @property string $name
 * @property string $title
 * @property string $url
 * @property string $controller_id
 * @property string $action_id
 * @property string $params
 * @property integer $sort_order
 * @property string $icon
 * @property string $image
 * @property string $css_class
 *
 * @property string|Array $urlTo
 *
 * @property MenuItem $parent
 * @property MenuItem[] $cilds
 * @property Menu $menu
 */
class MenuItem extends \yii\db\ActiveRecord {

    private $_params_parsed;
    public $level;
    public $has_children;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%menu_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['menu_id'], 'required', 'on' => 'create'],
            [['menu_id'], 'integer', 'on' => 'create'],
            [['menu_id'], 'exist', 'targetClass' => 'app\modules\menu\models\Menu', 'targetAttribute' => 'menu_id', 'on' => 'create'],
            [['name'], 'required'],
            [['parent_id', 'sort_order'], 'integer'],
            [['parent_id'], 'exist', 'targetClass' => 'app\modules\menu\models\MenuItem', 'targetAttribute' => 'menu_item_id'],
            [['parent_id'], '_vParent'],
            [['name','css_class'], 'string', 'max' => 64],
            [['title', 'url'], 'string', 'max' => 255],
            [['controller_id', 'action_id'], 'string', 'max' => 127],
            [['params', 'icon', 'image'], 'string', 'max' => 512],
        ];
    }

    public function _vParent() {
        if (!$this->parent_id) {
            $this->parent_id = null;
        } else {
            if (!$this->isNewRecord) {
                if ($this->parent_id == $this->menu_item_id) {
                    $this->addError('parent_id', Yii::t('menu','Attribute {attribute} can not link to its model.',['attribute'=>$this->getAttributeLabel('parent_id')]));
                    return;
                }
                $childs = $this->menu->getItemsAll($this->menu_item_id, 0);
                foreach ($childs as $child)
                    if ($child->menu_item_id == $this->parent_id) {
                        $this->addError('parent_id', Yii::t('menu','Attribute {attribute} can not point to a child model.',['attribute'=>$this->getAttributeLabel('parent_id')]));
                        return;
                    }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'menu_item_id' => Yii::t('menu', 'ID'),
            'menu_id' => Yii::t('menu', 'Menu'),
            'parent_id' => Yii::t('menu', 'Parent'),
            'name' => Yii::t('menu', 'Name'),
            'title' => Yii::t('menu', 'Title'),
            'url' => Yii::t('menu', 'URL'),
            'controller_id' => Yii::t('menu', 'Controller'),
            'action_id' => Yii::t('menu', 'Action'),
            'params' => Yii::t('menu', 'Params'),
            'sort_order' => Yii::t('menu', 'Sort order'),
            'icon' => Yii::t('menu', 'Icon'),
            'image' => Yii::t('menu', 'Image'),
        ];
    }

    /**
     * @return string|Array
     */
    public function getUrlTo() {
        if ($this->controller_id) {
            if (strpos($this->controller_id, '|')!==false) {
                $controller_id=explode('|', $this->controller_id)[Yii::$app->user->isGuest?0:1];
                if (!$controller_id) {return null;}
            }
            else {
                $controller_id=$this->controller_id;
            }
            $route = '/' . $controller_id;
            if ($this->action_id)
                $route.='/' . $this->action_id;
            else
                $route.='/index';
            $params = [];
            if ($this->params) {
                parse_str($this->params, $params);
            }
            return array_merge([$route], $params);
        } else {
            return $this->url;
        }
    }

    /**
     * @return mixed
     */
    public function getParam($key, $default = null) {
        if ($this->_params_parsed === null) {
            parse_str($this->params, $temp);
            $this->_params_parsed = $temp;
        }
        return \yii\helpers\ArrayHelper::getValue($this->_params_parsed, $key, $default);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(MenuItem::className(), ['menu_item_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChilds() {
        return MenuItem::find()
                        ->select(['i.*', 'IF(ic.menu_item_id IS NULL,0,1) as has_children'])
                        ->from(MenuItem::tableName() . ' i')
                        ->leftJoin(MenuItem::tableName() . ' ic', 'ic.parent_id=i.menu_item_id')
                        ->where(['i.menu_id' => $this->menu_id, 'i.parent_id' => $this->menu_item_id])
                        ->groupBy('i.menu_item_id')
                        ->orderBy('i.sort_order,i.name')
                        ->indexBy('menu_item_id')
                        ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu() {
        return $this->hasOne(Menu::className(), ['menu_id' => 'menu_id']);
    }

}
