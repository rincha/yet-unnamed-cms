<?php
namespace app\common\behaviors;
use Yii;
use yii\db\ActiveRecord;
/**
 *
 * @author rincha
 * 
 * @property yii\db\ActiveRecord $owner
 */

class ArManyManyBehavior extends \yii\base\Behavior {
    
    public $valuesAttribute='new_values';
    public $relatedAttribute='related';
    public $relatedPkName='related_id';
    public $throughClass='model_to_related';
    
    public $autoFill=true;
    
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT=>'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE=>'afterSave',
            ActiveRecord::EVENT_AFTER_FIND=>'afterFind',
            ActiveRecord::EVENT_AFTER_REFRESH=>'afterFind',
        ];
    }
    
    public function afterFind($event) {
        if ($this->autoFill) {
            $this->owner->{$this->valuesAttribute}=array_keys($this->getRelated());
        }
    }
    
    private function getRelated() {
        $relfuncname='get'.ucfirst($this->relatedAttribute);
        return $this->owner->{$relfuncname}()->indexBy($this->relatedPkName)->all();
    }
       
    public function afterSave($event) { 
        //if ($this->owner->{$this->valuesAttribute}!==null) {
        //var_dump($this->owner->{$this->valuesAttribute}); echo '<hr>';
        $related=$this->getRelated();
        $primary_key=$this->owner->getPrimaryKey(true);
        /*var $throughClass yii\db\ActiveRecord*/
        $throughClass=$this->throughClass;
        foreach ($related as $rel) {
            if (!in_array($rel->{$this->relatedPkName}, $this->owner->{$this->valuesAttribute})) {
                $throughClass::findOne($primary_key+[$this->relatedPkName=>$rel->{$this->relatedPkName}])->delete();
            }
            else {
                unset($this->owner->{$this->valuesAttribute}[array_search($rel->{$this->relatedPkName}, $this->owner->{$this->valuesAttribute})]);
            }
        }
        //var_dump($this->owner->{$this->valuesAttribute}); echo '<hr>';
        foreach ($this->owner->{$this->valuesAttribute} as $rel_id) {
            $model=new $throughClass();
            $model->{$this->relatedPkName}=$rel_id;
            foreach ($primary_key as $column=>$value) {
                $model->{$column}=$value;
            }
            $model->save();
        }
        //die();
        //}
    }
    
    
}
