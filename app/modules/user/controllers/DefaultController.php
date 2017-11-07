<?php

namespace app\modules\user\controllers;

use Yii;
use app\models\User;
use app\models\UserAuthentication;
use yii\filters\VerbFilter;
use app\modules\user\models\SecuritySettings;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

class DefaultController extends \app\common\web\DefaultController {

    public function behaviors() {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'authentication-delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $user = $this->findModel(Yii::$app->user->id);
        return $this->render('index', ['user' => $user]);
    }

    public function actionAuthentications() {
        $user = $this->findModel(Yii::$app->user->id);
        return $this->render('authentications', ['user' => $user]);
    }

    public function actionOauthCreate($type,$code) {
        $user = $this->findModel(Yii::$app->user->id);
        $authentication = new UserAuthentication();
        $authentication->scenario = 'add';
        $authentication->type=$type;
        if ($authentication->validate(['type'])) {
            $authentication->getTypeModel()->getClient()->fetchAccessToken($code);
            $attributes=$authentication->getTypeModel()->getClient()->getUserAttributes();
            if (!isset($attributes['id'])) {
                throw new BadRequestHttpException('wrong attributes recived');
            }
            $authentication->uid=$attributes['id'].'';
            $exist_authentication = $this->findAuthenicationModel($authentication->type, $authentication->uid, true);
            if ($exist_authentication && $exist_authentication->status==User::STATUS_NOACTIVE) {
                $authentication=$exist_authentication;
            }
            $authentication->user_id=$user->id;
            $authentication->status=UserAuthentication::STATUS_ACTIVE;
            if ($authentication->save()) {
                Yii::$app->session->setFlash('success',Yii::t('app/user','New account successfully connected!'));
                return $this->redirect(['/u/default/authentications']);
            }
            else {
                throw new BadRequestHttpException(strip_tags(\yii\helpers\Html::errorSummary($authentication)));
            }
        }
        throw new BadRequestHttpException();
    }

    public function actionAuthenticationCreate($type) {
        $user = $this->findModel(Yii::$app->user->id);
        $authentication = new UserAuthentication();
        $authentication->scenario = 'add';
        $authentication->user_id=$user->id;
        $authentication->type=$type;
        if ($authentication->validate(['type']) && $authentication->getTypeModel()->protocol && Yii::$app->request->isPost) {
            return $this->redirect($authentication->getTypeModel()->getClient()->buildAuthUrl([
                'redirect_uri'=> \yii\helpers\Url::to([
                    'oauth-create', 'type'=>$type,
                ], true)
            ]));
        }
        elseif ($authentication->load(Yii::$app->request->post())) {
            $exist_authentication = $this->findAuthenicationModel($authentication->type, $authentication->uid);
            if ($exist_authentication && $exist_authentication->status==User::STATUS_NOACTIVE) {
                $authentication=$exist_authentication;
            }
            $authentication->user_id=$user->id;
            $authentication->type=$type;
            $authentication->status=  UserAuthentication::STATUS_DRAFT;
            if ($authentication->validate() && !$authentication->typeModel->protocol) {
                $res = $authentication->typeModel->sendConfirm();
                if (isset($res['redirect'])) {
                    return $this->redirect($res['redirect']);
                }
            }
        }
        return $this->render('authentication-create', ['user' => $user,'authentication'=>$authentication]);
    }

    public function actionAuthenticationDelete($id) {
        $model=$this->findAuthenicationModelById($id);
        if ($model->user_id!=Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }
        if ($model->typeParams['required']) {
            throw new BadRequestHttpException(Yii::t('app/user','This account type is required. Your can`t delete it.'));
        }
        if (!$model->delete()) {
            throw new ServerErrorHttpException();
        }
        return $this->redirect(['authentications']);
    }

    public function actionSessions() {
        $user = $this->findModel(Yii::$app->user->id);
        return $this->render('sessions', ['user' => $user, 'sessions'=>Yii::$app->session->getUserSessions(Yii::$app->user->id)]);
    }

    public function actionSecurity() {
        $user = $this->findModel(Yii::$app->user->id);
        $user->scenario='password-change';
        $settings=new SecuritySettings;
        $settings->user_id=Yii::$app->user->id;
        $settings->loadFromUser();

        $result=null;
        if (Yii::$app->request->post('renew-auth-key')) {
            $user->generateAuthKey();
            $user->scenario='default';
            if ($user->save()) {
                Yii::$app->session->addFlash('flash.success', Yii::t('app/user', 'Auth token successfully changed.'));
            }
            else {
                Yii::$app->session->addFlash('flash.error', Yii::t('app/user', 'Auth token could not be changed.'));
            }
            $user->scenario='password-change';
        }
        if ($user->load(Yii::$app->request->post())) {
            if ($user->save()) {
                Yii::$app->session->addFlash('flash.success', Yii::t('app/user', 'Password successfully changed.'));
                $result=true;
            }
            else {
                $result=false;
            }
        }
        if ($settings->load(Yii::$app->request->post())) {
            if ($settings->save()) {
                Yii::$app->session->addFlash('flash.success', Yii::t('app/user', 'Security settings updated successfully.'));
                $result=$result===false?false:true;
            }
            else {
                $result=false;
            }
        }

        if ($result===true) {
            return $this->redirect(['security']);
        }

        return $this->render('security', ['user' => $user,'settings'=>$settings]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the UserAuthentication model.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $type
     * @param string $uid
     * @return UserAuthentication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findAuthenicationModel($type, $uid, $skip_null=false) {
        if (($model = UserAuthentication::findOne(['type' => $type, 'uid' => $uid])) !== null || $skip_null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
    protected function findAuthenicationModelById($id) {
        if (($model = UserAuthentication::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
