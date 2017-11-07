<?php

namespace app\modules\user;

use Yii;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\user\controllers';

    public $migrations = true;

    public function getLinksDefinition() {
        return [
            'moduleName' => Yii::t('app/user', 'User'),
            'controllers' => include __DIR__ . DIRECTORY_SEPARATOR . 'controllers.php',
        ];
    }

    public function init() {
        parent::init();
    }

}
