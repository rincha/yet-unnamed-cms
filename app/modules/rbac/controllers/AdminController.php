<?php

namespace app\modules\rbac\controllers;

use Yii;
use app\modules\rbac\models\AuthItem;
use app\modules\rbac\models\AuthItemChild;
use app\modules\rbac\models\AuthRule;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\rbac\components\ControllerHelper;

/**
 * AdminController implements the CRUD actions for AuthItem model.
 */
class AdminController extends \app\common\web\AdminController {

    public $layout = '@app/modules/admin/views/layouts/admin';

    public function behaviors() {
        return parent::behaviors()+[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'assignment-delete' => ['post'],
                    'auth-item-delete' => ['post'],
                    'auth-item-child-delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $model = new \app\models\UserSearch();
        $dataProvider = $model->search(Yii::$app->request->get());
        return $this->render('index', ['dataProvider' => $dataProvider, 'model' => $model]);
    }

    public function actionUserAuthView($id) {
        $model = $this->findModelUser($id);
        $post = Yii::$app->request->post();
        if (isset($post['AuthAssigment']) && is_array($post['AuthAssigment'])) {
            foreach ($post['AuthAssigment'] as $roleName) {
                $role = \Yii::$app->authManager->getRole($roleName);
                if ($role && !\Yii::$app->authManager->getAssignment($roleName, $model->id))
                    \Yii::$app->authManager->assign($role, $model->id);
            }
        }
        return $this->render('user_auth_view', [
            'model' => $model,
        ]);
    }

    public function actionAssignmentDelete($id, $user_id) {
        $model = $this->findModelUser($user_id);
        $role = \Yii::$app->authManager->getRole($id);
        if ($role) {
            \Yii::$app->authManager->revoke($role, $user_id);
        } else {
            throw new NotFoundHttpException('Role not found.');
        }
        return $this->redirect(['user-auth-view', 'id' => $user_id]);
    }

    public function actionAuthRuleIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => AuthRule::find(),
        ]);
        return $this->render('auth_rule_index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAuthDefaultRolesCreate() {
        foreach (Yii::$app->authManager->defaultRoles as $rolename) {
            if (!AuthItem::findOne($rolename)) {
                $role=new AuthItem();
                $role->name=$rolename;
                $role->type=  \yii\rbac\Item::TYPE_ROLE;
                $role->description=  \Yii::t('rbac', '{role} - default role',['role'=>$rolename]);
                if (isset(Yii::$app->authManager->defaultRolesConfig[$rolename])) {
                    foreach (Yii::$app->authManager->defaultRolesConfig[$rolename] as $attribute=>$value) {
                        $role->{$attribute}=$value;
                    }
                }
                $role->save();
            }
        }

        return $this->redirect(['auth-item-index']);
    }

    public function actionAuthControllerRolesCreate() {
        $data=Yii::$app->request->post('create');
        if (Yii::$app->request->isPost && $data && is_array($data)) {
            $success=0;
            foreach ($data as $val) {
                if (!AuthItem::findOne(['name'=>$val])) {
                $model = new AuthItem();
                $model->name=$val;
                $model->type=\yii\rbac\Item::TYPE_PERMISSION;
                if ($model->save()) {$success++;}
                }
            }
            Yii::$app->getSession()->setFlash('flash.success', Yii::t('rbac', '{count} auth item successfully added', ['count'=>$success]));
            return $this->redirect(['auth-controller-roles-create']);
        }
        $controllers=ControllerHelper::getAllControllers();
        return $this->render('auth_controller_roles_create', [
            'controllers' => $controllers,
        ]);
    }

    public function actionAuthRuleCreate() {
        $log = [];
        $defaultRulesPath = ['@app/modules/rbac/rules' => '\app\modules\rbac\rules'];
        $paths = $defaultRulesPath + $this->module->rulesPath;
        $rules = \Yii::$app->authManager->getRules();
        foreach ($paths as $path => $namespace) {
            $files = glob(Yii::getAlias($path) . '/*Rule.php');
            foreach ($files as $file) {
                $className = preg_replace('/^.*\/([a-z0-9_-]*)\.php$/ui', '$1', $file);
                $log[] = ['info', 'Check: ' . $namespace . $className . ' in file ' . $file];
                $className = $namespace . '\\' . $className;
                $rule = new $className();
                if (!isset($rules[$rule->name])) {
                    if (\Yii::$app->authManager->add($rule)) {
                        $log[] = ['success', 'Rule ' . $rule->name . ' created.'];
                    } else {
                        $log[] = ['danger', 'Rule ' . $rule->name . ' can not be created.'];
                    }
                } else {
                    $log[] = ['info', 'Rule ' . $rule->name . ' already exist.'];
                }
                $log[] = ['muted', '***'];
            }
        }
        return $this->render('auth_rule_create', [
                    'log' => $log,
        ]);
    }

    public function actionAuthRuleDelete($id) {
        $rule = \Yii::$app->authManager->getRule($id);
        if ($rule) {
            if (\Yii::$app->authManager->remove($rule)) {
                return $this->redirect(['auth-rule-index']);
            } else {
                throw new \yii\web\ServerErrorHttpException('Can not delete rule');
            }
        } else
            throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionAuthItemIndex() {
        $model = new \app\modules\rbac\models\AuthItemSearch();
        $dataProvider = $model->search(Yii::$app->request->get());
        /* $dataProvider = new ActiveDataProvider([
          'query' => AuthItem::find(),
          ]); */
        return $this->render('auth_item_index', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     */
    public function actionAuthItemView($id) {
        return $this->render('auth_item_view', [
                    'model' => $this->findModelAuthItem($id),
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAuthItemCreate() {
        $model = new AuthItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $save_childs = true;
            $post = Yii::$app->request->post('AuthItem');
            if (isset($post['new_children']) && is_array($post['new_children'])) {

                foreach ($post['new_children'] as $name) {
                    $child = AuthItem::findOne($name);
                    if ($child) {
                        $c = new AuthItemChild();
                        $c->child = $child->name;
                        $c->parent = $model->name;
                        if (!$c->save()) {
                            $save_childs = false;
                            $model->addError('new_children', strip_tags(\yii\helpers\Html::errorSummary($c)));
                        }
                    }
                }
            }
            if ($save_childs)
                return $this->redirect(['auth-item-view', 'id' => $model->name]);
        }
        return $this->render('auth_item_create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionAuthItemUpdate($id) {
        $model = $this->findModelAuthItem($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $save_childs = true;
            $post = Yii::$app->request->post('AuthItem');
            if (isset($post['new_children']) && is_array($post['new_children'])) {

                foreach ($post['new_children'] as $name) {
                    $child = AuthItem::findOne($name);
                    if ($child) {
                        $c = new AuthItemChild();
                        $c->child = $child->name;
                        $c->parent = $model->name;
                        if (!$c->save()) {
                            $save_childs = false;
                            $model->addError('new_children', strip_tags(\yii\helpers\Html::errorSummary($c)));
                        }
                    }
                }
            }

            if ($save_childs)
                return $this->redirect(['auth-item-view', 'id' => $model->name]);
        }
        return $this->render('auth_item_update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionAuthItemDelete($id) {
        $this->findModelAuthItem($id)->delete();
        return $this->redirect(['auth-item-index']);
    }

    public function actionAuthItemChildDelete($parent, $child) {
        $this->findModelAuthItemChild($parent, $child)->delete();
        return $this->redirect(['auth-item-update', 'id' => $parent]);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelAuthItem($id) {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelAuthItemChild($parent, $child) {
        if (($model = AuthItemChild::findOne(['parent' => $parent, 'child' => $child])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelUser($id) {
        if (($model = \app\models\User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
