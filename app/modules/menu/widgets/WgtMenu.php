<?php

namespace app\modules\menu\widgets;

use app\modules\menu\models\Menu;

class WgtMenu extends \yii\base\Widget {

    public $menu;
    public $cssClass;
    public $listOptions=[];

    public function run() {
        if (is_string($this->menu)) {
             $this->menu = Menu::findOne(['key' => $this->menu]);
        }
        elseif (is_numeric($this->menu)) {
            $this->menu = Menu::findOne($this->menu);
        }
        if ($this->menu instanceof Menu) {
            return $this->render($this->getViewType(), ['model' => $this->menu, 'class' => $this->cssClass ,'id'=>$this->id, 'listOptions'=>$this->listOptions]);
        }
        else {
            return null;
        }
    }

    private function getViewType() {
        if (file_exists($this->getViewPath() . DIRECTORY_SEPARATOR . 'menu_' . $this->menu->type . '.php')) {
            return 'menu_' . $this->menu->type;
        } else {
            return 'menu';
        }
    }

}

?>