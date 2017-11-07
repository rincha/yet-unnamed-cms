<?php

namespace app\modules\rbac\behaviors;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class RbacAccess extends Behavior {

    public $allowed_actions = [];

    public function events() {
        return [
            Controller::EVENT_BEFORE_ACTION => 'can'
        ];
    }

    public function can($event) {
        //return true, if action always allowed
        if (in_array($this->owner->action->id, $this->allowed_actions)) {
            Yii::trace('allow by allowed_actions', 'rbac');
            return true;
        }
        //return true, if user has root permission
        if (Yii::$app->user->can('root')) {
            Yii::trace('allow by root', 'rbac');
            return true;
        }

        $authItemId = [];
        if ($this->owner->module) {
            $authItemId[] = $this->owner->module->id;
            Yii::trace('this is module', 'rbac');
        }
        $authItemId[] = $this->owner->id;
        //check by pattern [module_id.]controller_id.* (all controller actions allowed)
        if (Yii::$app->user->can(implode('.', $authItemId) . '.*')) {
            Yii::trace('allow by controller', 'rbac');
            return true;
        } else {
            $authItemId[] = $this->owner->action->id;
            //check by pattern [module_id.]controller_id.action_id (one action)
            if (Yii::$app->user->can(implode('.', $authItemId))) {
                Yii::trace('allow by action', 'rbac');
                return true;
            } else {
                Yii::trace('disallow by action', 'rbac');
                throw new ForbiddenHttpException('Access denied');
            }
        }
        Yii::trace('disallow by unknown', 'rbac');
        return false;
    }

}
