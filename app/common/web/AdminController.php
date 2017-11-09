<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */
namespace app\common\web;

class AdminController extends \yii\web\Controller {

    public $layout = '@app/modules/admin/views/layouts/admin';

    public function behaviors() {
        return [
            'access' => [
                'class' => 'app\modules\rbac\behaviors\RbacAccess',
                'allowed_actions' => [],
            ],
        ];
    }

}
