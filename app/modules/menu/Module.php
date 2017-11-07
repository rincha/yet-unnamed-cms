<?php

namespace app\modules\menu;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\menu\controllers';
    public $migrations=true;

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
