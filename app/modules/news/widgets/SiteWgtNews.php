<?php

namespace app\modules\news\widgets;

use app\modules\news\models\News;
use app\modules\news\models\NewsType;
use Yii;

class SiteWgtNews extends \app\common\widgets\SiteWidget {
    
    private $_news;
    private $_type;

    public function run() {
        return $this->render(
                'news', 
                ['models' => $this->getNews(), 'id'=>$this->id, 'type'=>$this->getType()]
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
                'rules'=>['yii\validators\ExistValidator','targetClass'=>NewsType::className(), 'targetAttribute'=>'type_id', 'skipOnEmpty'=>true],
                'label'=>  Yii::t('news','Type'),
                'hint'=> Yii::t('news','Select news type or leave blank to display all'),
            ],            
        ];
    }
    
    public function getNews() {
        if ($this->_news) {return $this->_news;}
        if ($this->getType()) {
            $this->_news=News::find()->where(['type_id'=>$this->getType()->type_id])->orderBy(['date'=>SORT_DESC, 'created_at'=>SORT_DESC])->limit($this->options['count'])->all();
        }
        else {
            $this->_news=News::find()->orderBy(['date'=>SORT_DESC, 'created_at'=>SORT_DESC])->limit($this->options['count'])->all();
        }
        return $this->_news;
    }
    
    public function getType() {
        if ($this->_type) {return $this->_type;}
        if (is_numeric($this->options['type']) && $this->options['type']!=='' && $this->options['type']!==null) {
            $this->_type=  NewsType::findOne($this->options['type']);
        }  
        return $this->_type;
    }
    
    public function renderAdminView(\app\models\Widget $model, \yii\widgets\ActiveForm $form) {
        return $this->render('admin',['form'=>$form,'model'=>$model]);
    }
}

?>