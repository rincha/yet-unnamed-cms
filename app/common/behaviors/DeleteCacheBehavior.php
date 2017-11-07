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

class DeleteCacheBehavior extends \yii\base\Behavior {
    
    public $keysToDelete=[];
        
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT=>'afterChange',
            ActiveRecord::EVENT_AFTER_UPDATE=>'afterChange',
            ActiveRecord::EVENT_AFTER_DELETE=>'afterChange',            
        ];
    }
    
    public function afterChange($event) {
        foreach ($this->keysToDelete as $key) {
            Yii::$app->cache->delete($key);
        }
    }
    
    
    
    
}
