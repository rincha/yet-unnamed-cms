<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%widget}}".
 *
 * @property integer $widget_id
 * @property string $type
 * @property string $name
 * @property string $title
 * @property string $content
 * @property string $position
 * @property integer $sort_order
 * @property string $options
 * @property string $allow
 * @property string $deny
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \app\common\widgets\SiteWidget $wgt
 * @property string $positionName
 * @property string $typeName
 */
class Widget extends \yii\db\ActiveRecord
{
    private $_wgt;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%widget}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','name'], 'required'],
            [['type'], 'string', 'max' => 64],
            [['type'], 'in', 'range'=>array_keys(Yii::$app->params['widgets']['items'])],

            [['content', 'allow', 'deny'], 'string', 'max'=>64*1024],
            //[['content'],'filter', 'filter'=>'\app\common\helpers\AppHelper::htmlPurifyFull'],

            [['options'],'_vOptions'],
            [['options'],'filter', 'filter'=>'yii\helpers\Json::encode'],

            [['sort_order'], 'default', 'value'=>0],
            [['sort_order'], 'integer'],


            [['title','name'], 'string', 'max' => 255],

            [['position'], 'string', 'max' => 32],
            [['position'], 'in', 'range'=>array_keys(Yii::$app->params['widgets']['positions'])],
        ];
    }

    public function _vOptions() {
        if (!$this->hasErrors('type')) {
            if (!$this->wgt->validateOptionsAttributes()) {
                foreach ($this->wgt->getOptionsErrors() as $attribute=>$error) {
                    foreach ($error as $e) {
                        $this->addError('options['.$attribute.']', $e);
                    }
                }
            }
        }
    }

    public function behaviors() {
        return [
            TimestampBehavior::className()=>[
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    private function checkRoute($rule, $moduleId, $controllerId, $actionId) {
        $route_check=false;
        $temp=explode('?', $rule);
        $route=explode("/",$temp[0]);
        //module
        if (count($route)==3 && $moduleId!==null) {
            if (
                    ($route[0]=='*' || $route[0]==$moduleId)  &&
                    ($route[1]=='*' || $route[1]==$controllerId)  &&
                    ($route[2]=='*' || $route[2]==$actionId)
                ) {
                $route_check=true;
            }
        }
        //controller
        elseif (count($route)==2 && $moduleId===null) {
            if (
                    ($route[0]=='*' || $route[0]==$controllerId)  &&
                    ($route[1]=='*' || $route[1]==$actionId)
                ) {
                $route_check=true;
            }
        }
        //common
        elseif (count($route)==1 && $route[0]=='*') {
            $route_check=true;
        }
        return $route_check;
    }

    private function checkParams($rule,$queryParams) {
        $temp=explode('?', $rule);
        $params_check=true;
        parse_str(ArrayHelper::getValue($temp, 1), $params);
        $adv_params_value=ArrayHelper::getValue($params, '*', '!');
        if (isset($params['*'])) {
            unset($params['*']);
        }
        ksort($params);
        ksort($queryParams);
        //var_dump($params);
        $adv_params=$queryParams;
        foreach ($params as $k=>$v) {
            if (mb_strpos($v, '*', 0, 'UTF-8')==mb_strlen($v,'UTF-8')-1) {
                $pattern='/^'.  preg_quote(mb_substr($v, 0, mb_strlen($v,'UTF-8')-1, 'UTF-8')).'/ui';
            }
            else {
                $pattern='/^\*$/ui';
            }
            if (isset($queryParams[$k]) && ($queryParams[$k]==$v || $v=='*' || preg_match($pattern, $queryParams[$k]))) {
                unset($adv_params[$k]);
            }
            else {
                $params_check=false;
                break;
            }
        }
        if ($params_check) {
            if (count($adv_params)>0) {
                foreach ($adv_params as $k=>$v) {
                    if ($adv_params_value!=$v && $adv_params_value!='*') {
                        $params_check=false;
                        break;
                    }
                }
            }
        }
        return $params_check;
    }


    public function isAllow($controllerId, $actionId, $queryParams) {
        Yii::trace('widget (#'.$this->widget_id.':'.$this->title.'): check display options');

        $temp=explode('/', $controllerId);
        if (count($temp)==2) {
            $moduleId=$temp[0];
            $controllerId=$temp[1];
        }
        else {
            $moduleId=null;
        }

        $allow=!$this->allow;
        $deny=!!$this->deny;

        if (!$allow) {
            $allow_rules=array_map('trim', explode("\n", $this->allow));
            foreach ($allow_rules as $rule) {
                if ($this->checkRoute($rule, $moduleId, $controllerId, $actionId) && $this->checkParams($rule, $queryParams)) {
                    Yii::trace('widget (#'.$this->widget_id.':'.$this->title.'): allow by rule: '.$rule);
                    $allow=true;
                    break;
                }
                else {
                    Yii::trace('widget (#'.$this->widget_id.':'.$this->title.'): not passed by rule: '.$rule);
                }
            }
        }
        else {
            Yii::trace('widget (#'.$this->widget_id.':'.$this->title.'): allow by default');
        }

        if ($deny) {
            $deny=false;
            $deny_rules=array_map('trim', explode("\n", $this->deny));
            foreach ($deny_rules as $rule) {
                if ($this->checkRoute($rule, $moduleId, $controllerId, $actionId) && $this->checkParams($rule, $queryParams)) {
                    Yii::trace('widget (#'.$this->widget_id.':'.$this->title.'): deny by rule: '.$rule);
                    $deny=true;
                    break;
                }
                else {
                    Yii::trace('widget (#'.$this->widget_id.':'.$this->title.'): not deny by rule: '.$rule);
                }
            }
        }
        Yii::trace('widget (#'.$this->widget_id.':'.$this->title.'): '.(!$deny&&$allow?'allow':'deny'));
        return !$deny&&$allow;

    }

    public function getWgt() {
        if ($this->_wgt!==null) {return $this->_wgt;}
        else {
            $className=Yii::$app->params['widgets']['items'][$this->type]['class'];
            $this->_wgt=new $className(['widget'=>$this]);
            return $this->_wgt;
        }
    }

    public function getOptionsValue($key) {
        return ArrayHelper::getValue($this->options, $key);
    }

    public function getPositionName() {
        return \yii\helpers\ArrayHelper::getValue($this->positionsList(), $this->position);
    }

    public function getTypeName() {
        return \yii\helpers\ArrayHelper::getValue($this->typesList(), $this->type);
    }

    public function typesList() {
        $list=Yii::$app->params['widgets']['items'];
        $res=[];
        foreach ($list as $type=>$params) {
            $res[$type]=Yii::t('app/widgets', $params['name']);
        }
        return $res;
    }

    public function positionsList() {
        $list=Yii::$app->params['widgets']['positions'];
        $res=[];
        foreach ($list as $name=>$title) {
            $res[$name]=Yii::t('app/widgets', $title);
        }
        return $res;
    }

    public function afterFind() {
        if (is_string($this->options)) {
            $this->options=  \yii\helpers\Json::decode($this->options,true);
        }
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'widget_id' => Yii::t('app/widgets', 'Widget ID'),
            'type' => Yii::t('app/widgets', 'Type'),
            'name' => Yii::t('app/widgets', 'Name'),
            'title' => Yii::t('app/widgets', 'Title'),
            'content' => Yii::t('app/widgets', 'Content'),
            'position' => Yii::t('app/widgets', 'Position'),
            'sort_order' => Yii::t('app/widgets', 'Sort Order'),
            'options' => Yii::t('app/widgets', 'Options'),
            'allow' => Yii::t('app/widgets', 'Allow'),
            'deny' => Yii::t('app/widgets', 'Deny'),
            'created_at' => Yii::t('app/widgets', 'Created At'),
            'updated_at' => Yii::t('app/widgets', 'Updated At'),
        ];
    }

     public function attributeHints() {
        return [
            'allow'=>Yii::t('app/widgets', 'Rule format: [module_id]/controller_id/action_id[?param1=value1&amp;param2=value2...]<br>'
                    . 'Each rule in a new line. * - replaces any ID, param or value. '),
        ];
    }
}
