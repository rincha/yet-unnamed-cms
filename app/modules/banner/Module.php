<?php

namespace app\modules\banner;

/**
 * banner module definition class
 */
class Module extends \yii\base\Module {

    public $migrations = true;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\banner\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // custom initialization code goes here
    }

}
