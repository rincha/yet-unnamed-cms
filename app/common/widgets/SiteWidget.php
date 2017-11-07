<?php
namespace app\common\widgets;
use yii\helpers\ArrayHelper;

/**
 * @author rincha
 *
 * @property app\models\Widget $widget
 * @property Array $options read-only
 */
abstract class SiteWidget extends \yii\base\Widget {

    public $widget;

    private $_optionsErrors=[];

    /*
     * return Array
     * [
     *      'attributeName'=>[
     *          'rules'=>[validation rules],
     *          'label'=>'attribute label',
     *          'hint'=>'attribute hint',
     *          'defaultValue'=>'default attribute value',
     *      ],
     * ]
     */
    abstract public function getOptionsAttributes();
    /*
     * return boolean
     */
    public function validateOptionsAttributes() {
        foreach ($this->getOptionsAttributes() as $attribute=>$params) {
            $validator=  array_shift($params['rules']);

            if (is_callable($validator)) {
                if (!$validator(ArrayHelper::getValue($this->options, $attribute),$params['rules'])) {
                    $this->addOptionsError($attribute, Yii::t('app/widgets', 'Attribute {attribute} is invalid.',['attribute'=>$params['label']]));
                }
            }
            elseif ($validator) {
                if (!ArrayHelper::getValue($params['rules'], 'skipOnEmpty') || ArrayHelper::getValue($this->options, $attribute)!=='') {
                    $v=new $validator($params['rules']);
                    if (!$v->validate(ArrayHelper::getValue($this->options, $attribute),$error)) {
                        $this->addOptionsError($attribute, $params['label'].': '.$error);
                    }
                }
            }
        }
        return !$this->hasOptionsErrors();
    }

    public function getOptionsErrors() {
        return $this->_optionsErrors;
    }

    public function addOptionsError($attribute,$error) {
        $this->_optionsErrors[$attribute][]=$error;
    }

    public function hasOptionsErrors() {
        return !empty($this->_optionsErrors);
    }

    public function getOptions() {
        if (is_array($this->widget->options)) {
            return $this->widget->options;
        }
        else {
            return \yii\helpers\Json::decode($this->widget->options,true);
        }
    }

    public function getOption($option) {
        return ArrayHelper::getValue($this->options, $option);
    }

    public function renderAdminView(\app\models\Widget $model, \yii\widgets\ActiveForm $form) {
        $res='';
        foreach ($this->getOptionsAttributes() as $attribute=>$params) {
            $res.=$form->field($model, 'options['.$attribute.']')
                    ->textInput([])
                    ->label($params['label'])
                    ->hint(ArrayHelper::getValue($params, 'hint'));
        }
        return $res;
    }
}
