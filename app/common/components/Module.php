<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\components;

class Module extends \yii\base\Module {

    /**
     * @var boolean need to install migrations for this module
     */
    public $migrations = false;

    /**
     * @var boolean need to install migrations for demo data
     */
    public $migrationsDemo = false;

    /**
     * @return array
     */
    public function getLinksDefinition(){
        return [];
    }

}
