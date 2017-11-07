<?php

namespace app\modules\rbac;

class RbacManager extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\rbac\controllers';
	
    public $rulesPath=[];
    
    public function init()
    {
        parent::init();
    }
}
