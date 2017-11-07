<?php

namespace app\modules\info\widgets;

use app\modules\info\models\Info;
use app\modules\info\models\Type;
use Yii;

class SiteWgtInfo extends \app\common\widgets\SiteWidget {
    
    private $_models;
    private $_type;

    public function run() {
        return $this->render(
                'info', 
                ['models' => $this->getModels(), 'id'=>$this->id, 'type'=>$this->getType()]
        );
    }
    
    public function getOptionsAttributes() {
        return [
            'count'=>[
                'rules'=>['yii\validators\NumberValidator','min'=>1, 'max'=>100, 'integerOnly'=>true],
                'label'=>  Yii::t('news','Count'),
                'hint'=>null,
            ],
            'type'=>[
                'rules'=>['yii\validators\ExistValidator','targetClass'=>Type::className(), 'targetAttribute'=>'type_id', 'skipOnEmpty'=>false],
                'label'=>  Yii::t('info','Type'),
                'hint'=> null,
            ],
            'more_text'=>[
                'rules'=>['yii\validators\StringValidator','min'=>1, 'max'=>100],
                'label'=>  Yii::t('info','Text on link "See all"'),
                'hint'=>null,
            ]
        ];
    }
    
    public function getModels() {
        if ($this->_models) {return $this->_models;}
        if ($this->getType()) {
            $this->_models=Info::find()->where(['type_id'=>$this->getType()->type_id])->orderBy(['date'=>SORT_DESC, 'created_at'=>SORT_DESC])->limit($this->options['count'])->all();
        }
        else {
            $this->_models=Info::find()->orderBy(['date'=>SORT_DESC, 'created_at'=>SORT_DESC])->limit($this->options['count'])->all();
        }
        return $this->_models;
    }
    
    public function getType() {
        if ($this->_type) {return $this->_type;}
        if (is_numeric($this->options['type']) && $this->options['type']!=='' && $this->options['type']!==null) {
            $this->_type=  Type::findOne($this->options['type']);
        }  
        return $this->_type;
    }
    
    public function renderAdminView(\app\models\Widget $model, \yii\widgets\ActiveForm $form) {
        return $this->render('admin',['form'=>$form,'model'=>$model]);
    }
}

?>