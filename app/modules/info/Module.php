<?php

namespace app\modules\info;

use Yii;
use app\modules\info\models\RelationType;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\info\controllers';
    public $enableIndexAction = true;
    public $enableIndexActionWithoutType = false;
    public $migrations = true;

    public function getLinksDefinition() {
        return [
            'moduleName' => Yii::t('info', 'Information materials'),
            'controllers' => include __DIR__ . DIRECTORY_SEPARATOR . 'controllers.php',
        ];
    }

    public function init() {
        parent::init();

        // custom initialization code goes here
    }

    protected static $_relation_types;

    public static function getRelationTypes() {
        if (self::$_relation_types === null) {
            self::$_relation_types = RelationType::find()->indexBy('name')->all();
        }
        return self::$_relation_types;
    }

}
