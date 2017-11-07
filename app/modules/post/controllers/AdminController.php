<?php

namespace app\modules\post\controllers;

use Yii;
use yii\helpers\Html;
use app\modules\post\models\SettingsForm;
use app\models\User;
use app\models\UserSearch;
use app\modules\post\Module;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

/**
 * Admin controller for the `post` module
 */
class AdminController extends \app\common\web\AdminController {

    public function behaviors() {
        return parent::behaviors()+[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'users-revoke' => ['post'],
                    'users-assign' => ['post'],
                ],
            ],
        ];
    }

    public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>'<i class="fa fa-file-o"></i> '.Html::tag('span', Yii::t('post', 'Posts'),['class'=>'text']), 'encode'=>false, 'url' => ['/post/admin/index'], 'options'=>['title'=>Yii::t('post', 'Posts')]]
            ]
        ];
    }

    public function actionIndex() {
        return $this->render('index', [

        ]);
    }

    public function actionUsers() {
        $searchModel = new UserSearch();
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel->status= UserSearch::STATUS_ACTIVE;
        $dataProvider = $searchModel->search([]);
        return $this->render('users', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionUsersRevoke($id,$role) {
        $this->setUserRole($id, $role, false);
        return $this->redirect(['users']);
    }

    public function actionUsersAssign($id,$role) {
        $this->setUserRole($id, $role, true);
        return $this->redirect(['users']);
    }

    public function actionSettings() {
        $model=new SettingsForm();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('flash.success',Yii::t('app','Operation completed successfully'));
            return $this->redirect(['settings']);
        }
        else {
            return $this->render('settings', [
                'model' => $model,
            ]);
        }
    }

    private function setUserRole($id, $roleName, $type) {
        $user=$this->findUser($id);
        $roleNames=[Module::ROLE_AUTHOR,Module::ROLE_ADMIN];
        if (!in_array($roleName, $roleNames)) {
            throw new BadRequestHttpException('You can revoke/assign only this roles: '. implode(',', $roleNames));
        }
        $role = Yii::$app->authManager->getRole($roleName);
        if ($type) {
            if ($role && !\Yii::$app->authManager->getAssignment($roleName, $user->id)) {
                    Yii::$app->authManager->assign($role, $user->id);
            }
        }
        elseif ($role) {
            Yii::$app->authManager->revoke($role, $user->id);
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUser($id) {
        if (($model = User::findOne($id)) !== null && $model->status==User::STATUS_ACTIVE) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
