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

class DateConvertBehavior extends \yii\base\Behavior {

    public $attributes=[];
    public $dbFormat='php:Y-m-d';
    public $viewFormat=null;

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE=>'convertDatesDB',
            ActiveRecord::EVENT_BEFORE_UPDATE=>'convertDatesDB',
            ActiveRecord::EVENT_BEFORE_INSERT=>'convertDatesDB',
            ActiveRecord::EVENT_AFTER_FIND=>'convertDatesView',
            ActiveRecord::EVENT_AFTER_VALIDATE=>'convertDatesView',
        ];
    }

    public function convertDatesDB($event) {
        foreach ($this->attributes as $attribute) {
            if ($this->owner->{$attribute}) {
                $this->owner->{$attribute}=\Yii::$app->formatter->asDate(strtotime($this->owner->{$attribute}), $this->dbFormat);
            }
        }
    }

    public function convertDatesView($event) {
        if ($this->viewFormat===null) {
            $this->viewFormat=\Yii::$app->formatter->dateFormat;
        }
        foreach ($this->attributes as $attribute) {
            if ($this->owner->{$attribute}) {
                $this->owner->{$attribute}=\Yii::$app->formatter->asDate(strtotime($this->owner->{$attribute}), $this->viewFormat);
            }
        }
    }
}
