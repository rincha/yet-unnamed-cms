<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\web;

use Yii;

class DefaultController extends \yii\web\Controller {

    /*
     * return Array
     */
    public static function apiAdmin() {
        return [
            'name'=>$this->uniqueId,
            'actions'=>[],
        ];
    }

    public $rbacEnable=true;

    public function behaviors() {
        if ($this->rbacEnable) {
            return [
                'access' => [
                    'class' => 'app\modules\rbac\behaviors\RbacAccess',
                    'allowed_actions' => [],
                ],
            ];
        }
        else {
            return [];
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction( $action) {
        if (parent::beforeAction($action)) {

            if (isset(Yii::$app->view->controllersLayout[$this->uniqueId])) {
                $cl=Yii::$app->view->controllersLayout[$this->uniqueId];
                if (is_string($cl)) {
                    $this->layout=$cl;
                }
                elseif (is_array($cl) && isset($cl[$action->id])) {
                    $this->layout=$cl[$action->id];
                }
            }

            $widgets=\app\models\Widget::find("position!='none'")->orderBy(['sort_order'=>SORT_ASC])->all();
            foreach ($widgets as $widget) {
                if ($widget->isAllow($this->uniqueId,$action->id,Yii::$app->request->get())) {
                    View::$widgets[$widget->position][$widget->widget_id]=$widget;
                }
            }
            return true;
        }
        else {
            return false;
        }
    }

}
