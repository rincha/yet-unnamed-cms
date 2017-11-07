<?php
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
