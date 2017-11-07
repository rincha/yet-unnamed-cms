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

class DatetimeConvertBehavior extends \yii\base\Behavior {

    public $attributes=[];
    public $dbFormat='php:Y-m-d H:i:s';
    public $viewFormat=null;
    public $events=null;

    public function events() {
        if ($this->events===null) {
        return [
            //ActiveRecord::EVENT_BEFORE_VALIDATE=>'convertDatesDB',
            ActiveRecord::EVENT_BEFORE_UPDATE=>'convertDatesDB',
            ActiveRecord::EVENT_BEFORE_INSERT=>'convertDatesDB',
            ActiveRecord::EVENT_AFTER_FIND=>'convertDatesView',
            //ActiveRecord::EVENT_AFTER_VALIDATE=>'convertDatesView',
        ];
        }
        else {
            return $this->events;
        }
    }

    public function convertDatesDB($event) {
        foreach ($this->attributes as $attribute) {
            if ($this->owner->{$attribute}) {
                $this->owner->{$attribute}=Yii::$app->formatter->asDatetime(
                    strtotime($this->owner->{$attribute}),
                    $this->dbFormat
                );
            }
        }
    }

    public function convertDatesView($event) {
        if ($this->viewFormat===null) {
            $this->viewFormat=Yii::$app->formatter->datetimeFormat;
        }
        foreach ($this->attributes as $attribute) {
            if ($this->owner->{$attribute}) {
                $this->owner->{$attribute}=Yii::$app->formatter->asDatetime(
                        strtotime($this->owner->{$attribute}),
                        $this->viewFormat
                );
            }
        }
    }

    public function _vDateTime($attribute, $params) {
        if (mb_strpos($this->viewFormat, 'php:', 0, 'UTF-8')===0) {
            $format=mb_substr($this->viewFormat, 4, null, 'UTF-8');
        }
        else {
            $format=$this->viewFormat;
        }
        $f = \DateTime::createFromFormat($format, $this->owner->{$attribute});
        $valid = \DateTime::getLastErrors();
        if ($valid['warning_count'] || $valid['error_count']) {
            $this->owner->addError($attribute, Yii::t('yii', 'The format of {attribute} is invalid.', ['attribute'=>$this->owner->getAttributeLabel($attribute)]));
        }
    }
}
