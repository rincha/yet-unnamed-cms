<?php

namespace app\modules\menu\widgets;

use app\modules\menu\models\Menu;
use Yii;

class SiteWgtMenu extends \app\common\widgets\SiteWidget {

    private $_menu;

    public function run() {
        if ($this->getMenu() instanceof Menu) {
            return $this->render($this->getViewType(), [
                'model' => $this->getMenu(),
                'widget' => $this->widget,
                'class' => $this->options['cssClass'] ,
                'id'=>$this->id]);
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

    public function getOptionsAttributes() {
        return [
            'menu'=>[
                'rules'=>['yii\validators\ExistValidator','targetClass'=>Menu::className(), 'targetAttribute'=>'key'],
                'label'=>  Yii::t('menu','Menu'),
                'hint'=>null,
            ],
            'cssClass'=>[
                'rules'=>['yii\validators\StringValidator','max'=>128],
                'label'=>  Yii::t('menu','Css class'),
                'hint'=>null,
            ],
        ];
    }

    public function getMenu() {
        if ($this->_menu) {return $this->_menu;}
        if (is_string($this->options['menu'])) {
            $this->_menu=Menu::findOne(['key' => $this->options['menu']]);
        }
        elseif (is_numeric($this->options['menu'])) {
            $this->_menu=Menu::findOne($this->options['menu']);
        }
        return $this->_menu;
    }

    public function renderAdminView(\app\models\Widget $model, \yii\widgets\ActiveForm $form) {
        return $this->render('admin',['form'=>$form,'model'=>$model]);
    }
}

?>