<?php

namespace app\common\web;

/**
 * @author rincha
 *
 * @property Array $widgets read-only
 * @property Array $assets read-only
 */
class View extends \yii\web\View {

    public static $widgets;
    public $h1;
    public $controllersLayout;

    /**
     * @return Array[]app\models\Widget[]
     */
    public function getWidgets() {
        return self::$widgets;
    }

    public function hasWidgets($position) {
        return isset($this->widgets[$position])&&!empty($this->widgets[$position]);
    }

    public function runWidgets($position) {
        $res='';
        if ($this->hasWidgets($position)) {
            foreach ($this->widgets[$position] as $w) {
                if ($w instanceof \app\models\Widget) {
                    $res.=$w->wgt->run();
                }
                elseif ($w instanceof \yii\base\Widget) {
                    $res.=$w->run();
                }
                elseif (is_string($w)) {
                    $res.=$w;
                }
            }
        }
        return $res;
    }

}
