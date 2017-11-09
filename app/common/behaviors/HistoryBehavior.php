<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\behaviors;
use Yii;
use yii\db\ActiveRecord;

/**
 *
 * @author rincha
 *
 * @property yii\db\ActiveRecord $owner
 */

class HistoryBehavior extends \yii\base\Behavior {

    public $historyClass=null;
    public $historyFk=null;
    public $historyAttributes=['status'];
    public $eventAttributes=['status'];

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT=>'addHistory',
            ActiveRecord::EVENT_AFTER_UPDATE=>'afterUpdate',
        ];
    }

    public function afterUpdate($event) {
        $changed=[];
        foreach ($this->eventAttributes as $attribute) {
            if (isset($event->changedAttributes[$attribute]) && $event->changedAttributes[$attribute]!=$this->owner->{$attribute}) {
                $changed[]=$attribute;
            }
        }
        if ($changed) {
            $this->addHistory($event);
        }
    }

    public function addHistory($event) {
        if ($this->historyClass===null) {
            $this->historyClass=$this->owner->className().'History';
        }
        if ($this->historyFk===null) {
            $this->historyFk=$this->owner->primaryKey();
            if (is_array($this->historyFk) && count($this->historyFk)===1) {
                $this->historyFk=$this->historyFk[0];
            }
            else {
                throw new Exception('Compatible only model with a simple primary key.');
            }
        }
        $historyClassname=$this->historyClass;
        $history=new $historyClassname;
        $history->event=Yii::$app->user->id.':'.$event->name;
        $history->{$this->historyFk}=$this->owner->getPrimaryKey();
        foreach ($this->historyAttributes as $attribute) {
            $history->{$attribute}=$this->owner->{$attribute};
        }
        $history->save();
    }

}
