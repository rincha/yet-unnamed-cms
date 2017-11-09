<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserLoginTry;
use app\models\LoginForm;
use app\models\UserAuthentication;
use app\models\UserRestore;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;

class UserController extends \app\common\web\DefaultController {

    public $rbacEnable=false;

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['u/default/index']);
        }
        $model = new LoginForm();
        $model->scenario = 'login';
        if ($model->load(Yii::$app->request->post())) {
            if ($model->user) {
                $try=$model->user->getLoginTry()
                        ->where([
                            '>',
                            'updated_at',
                            date('Y-m-d H:i:s', time()-UserLoginTry::TRY_INTERVAL)
                        ])
                        ->one();
                if ($try && $try->count >= Yii::$app->user->authentications['max_login_try']) {
                    $model->scenario = 'login-captcha';
                    $model->load(Yii::$app->request->post());
                }
                elseif (!$try) {
                    $model->user->clearLoginTry();
                }
            }
            if ($model->validate()) {
                $duration = Yii::$app->request->post('rememberMe') ? Yii::$app->user->rememberCookieLifetime : 0;
                if (Yii::$app->user->login($model->user, $duration)) {
                    $model->user->clearLoginTry();
                    if (Yii::$app->user->returnUrl) {
                        return $this->redirect(\Yii::$app->user->returnUrl);
                    }
                    else {
                        return $this->redirect(['u/default/index']);
                    }
                }
            }
            elseif ($model->user) {
                $model->user->addLoginTry();
            }
        }
        return $this->render('login', [
                    'model' => $model
        ]);
    }

    public function actionLoginOauth($type,$code=null) {
        $authentication = new UserAuthentication();
        $authentication->type=$type;
        if (!$authentication->validate(['type'])) {
            throw new BadRequestHttpException('wrong type recived');
        }
        if ($code===null && $authentication->getTypeModel()->protocol) {
            return $this->redirect($authentication->getTypeModel()->getClient()->buildAuthUrl([
                'redirect_uri'=> \yii\helpers\Url::to([
                    'login-oauth', 'type'=>$type,
                ], true)
            ]));
        }
        elseif ($code!==null && $authentication->getTypeModel()->protocol) {
            $authentication->getTypeModel()->getClient()->fetchAccessToken($code);
            $attributes=$authentication->getTypeModel()->getClient()->getUserAttributes();
            if (!isset($attributes['id'])) {
                throw new BadRequestHttpException('wrong attributes recived');
            }
            $authentication=$this->findAuthenicationModel($type, $attributes['id']);
            if ($authentication) {
                if (Yii::$app->user->login($authentication->user, 0)) {
                    if (Yii::$app->user->returnUrl) {
                        return $this->redirect(Yii::$app->user->returnUrl);
                    }
                    else {
                        return $this->redirect(['u/default/index']);
                    }
                }
            }
            else {
                throw new NotFoundHttpException('user not found');
            }
        }
        else {
            throw new BadRequestHttpException('unknown error');
        }

    }

    public function actionLogout() {
        if (Yii::$app->user->logout()) {
            /*TODO fix for wrong domain in cookies*/
            setcookie('_identity','',time()-3600,'/','.'.$_SERVER['SERVER_NAME']);
            setcookie('PHPSESSID','',time()-3600,'/','.'.$_SERVER['SERVER_NAME']);
        }
        return $this->goHome();
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'activation' page.
     * @return mixed
     */
    public function actionRegister() {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/u/default/index']);
        }
        $model = new User();
        $model->scenario = 'register';
        $model->status=User::STATUS_NOACTIVE;

        $authentication = new UserAuthentication();
        $authentication->scenario = 'register';

        $profiles=[];

        $data=Yii::$app->request->post();

        if ($model->load($data) && $authentication->load($data)) {


            $profiles=$this->validateProfiles($model, $data);
            $isProfilesValid=!$this->hasProfilesErrors($profiles);

            $exist_authentication = UserAuthentication::findOne(['type' => $authentication->type, 'uid' => $authentication->uid]);
            if ($exist_authentication && $exist_authentication->status==UserAuthentication::STATUS_DRAFT) {
                $authentication=$exist_authentication;
            }
            if ($authentication->validate() && !$authentication->typeModel->protocol && $isProfilesValid) {
                    $transaction = $model->getDb()->beginTransaction();
                    if ($model->save()) {

                        $profiles=$this->addProfiles($model, $data);
                        if (!$this->hasProfilesErrors($profiles)) {
                            $authentication->user_id = $model->id;
                            $res = $authentication->typeModel->sendConfirm();
                            if (isset($res['redirect'])) {
                                $transaction->commit();
                                return $this->redirect($res['redirect']);
                            } else {
                                $transaction->rollBack();
                            }
                        }
                        else {
                            $transaction->rollBack();
                        }
                    }
            }
            $model->validate(); $authentication->validate();
        }

        return $this->render('register', [
                    'model' => $model,
                    'authentication' => $authentication,
                    'profiles'=>$profiles,
        ]);
    }

    /**
     * @param User $user
     * @param Array $data
     * @return \yii\db\ActiveRecord[] of Profile models
     */
    private function validateProfiles($user,$data) {
        $profiles=[];
        foreach (Yii::$app->user->profilesRequired as $id=>$options) {
            $profiles[$id]=$this->validateProfile($user, Yii::$app->user->profiles[$id], $data);
        }
        return $profiles;
    }

    /**
     * @param User $user
     * @param Array $profile - row from app\common\components\User::$profiles
     * @param Array $data
     * @return \yii\db\ActiveRecord of Profile model
     */
    private function validateProfile($user,$profile,$data) {
        $className=$profile['class'];
        $model=$user->{$profile['property']}?$user->{$profile['property']}:new $className;
        $model->scenario='register';
        $model->load($data);
        $model->user_id=$user->id;
        $model->validate();
        return $model;
    }

    /**
     * @param User $user
     * @param Array $data
     * @return \yii\db\ActiveRecord[] of Profile models
     */
    private function addProfiles($user,$data) {
        $profiles=[];
        foreach (Yii::$app->user->profilesRequired as $id=>$options) {
            $profiles[$id]=$this->addProfile($user, Yii::$app->user->profiles[$id], $data);
        }
        return $profiles;
    }

    /**
     * @param User $user
     * @param Array $profile - row from app\common\components\User::$profiles
     * @param Array $data
     * @return \yii\db\ActiveRecord of Profile model
     */
    private function addProfile($user,$profile,$data) {
        $className=$profile['class'];
        $model=$user->{$profile['property']}?$user->{$profile['property']}:new $className;
        $model->scenario='register';
        $model->load($data);
        $model->user_id=$user->id;
        $model->save();
        return $model;
    }

    /**
     * @param \yii\db\ActiveRecord[] $profiles of Profile model
     * @return boolean
     */
    private function hasProfilesErrors($profiles) {
        $res=false;
        foreach ($profiles as $profile) {
            if ($profile->hasErrors()) {
                $res=true;
            }
        }
        return $res;
    }

    public function actionActivateSend($type=null, $uid=null) {
        $uap = Yii::$app->user->authentications;
        if (!$uap || !$uap['enabled']) {
            throw new \yii\base\NotSupportedException(Yii::t('app', 'This method is not implemented.'));
        }
        $model= new UserAuthentication();
        $model->scenario='acitivate';
        $model->type=$type;
        $model->uid=$uid;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model = $this->findAuthenicationModel($model->type, $model->uid);
            if ($model->status !== UserAuthentication::STATUS_NOACTIVE) {
                throw new \yii\web\BadRequestHttpException(Yii::t('app/user', 'This account does not require activation (status: {status}).',['status'=>$model->statusName]));
            }
            $transaction = $model->getDb()->beginTransaction();
            $res = $model->typeModel->sendConfirm();
            if (isset($res['redirect'])) {
                $transaction->commit();
                return $this->redirect($res['redirect']);
            } else {
                Yii::$app->session->setFlash('flash.error', \yii\helpers\VarDumper::dumpAsString($res['errors']));
                $transaction->rollBack();
            }
        }
        return $this->render('activate-send', [
            'model' => $model,
        ]);
    }

    public function actionActivate($type, $uid, $verification = null) {
        $uap = Yii::$app->user->authentications;
        if (!$uap || !$uap['enabled']) {
            throw new \yii\base\NotSupportedException(Yii::t('app', 'This method is not implemented.'));
        }

        $model = $this->findAuthenicationModel($type, $uid);
        $user_need_activate=$model->status !== UserAuthentication::STATUS_DRAFT;

        if ($model->status !== UserAuthentication::STATUS_NOACTIVE && $model->status !== UserAuthentication::STATUS_DRAFT) {
            throw new \yii\web\BadRequestHttpException(Yii::t('app/user', 'This account does not require activation (status: {status}).',['status'=>$model->statusName]));
        }

        if ($model->verification_expire < date('Y-m-d H:i:s')) {
            Yii::$app->session->setFlash('flash.error', Yii::t('app/user', 'Verification time is expired.'));
            return $this->redirect(['activate-send', 'type' => $type, 'uid' => $uid]);
        }
        elseif (!$model->verification) {
            Yii::$app->session->setFlash('flash.error', Yii::t('app/user', 'Verification code not set.'));
            return $this->redirect(['activate-send', 'type' => $type, 'uid' => $uid]);
        }

        if (!$verification) {
            $verification=Yii::$app->request->get('code',null);
        }

        if ($verification) {
            if ($model->verification !== trim($verification)) {
                Yii::$app->session->setFlash('flash.error', Yii::t('app/user', 'Verification code is not valid.'));
            } else {
                $transaction = $model->getDb()->beginTransaction();
                $model->verification = null;
                $model->verification_expire = null;
                $model->status = User::STATUS_ACTIVE;
                if ($model->save()) {
                    $model->user->status = User::STATUS_ACTIVE;
                    if (!$user_need_activate || $model->user->save()) {
                        Yii::$app->session->setFlash('flash.success', Yii::t('app/user', 'Account activation compleate successfully!'));
                        $transaction->commit();
                        return $this->redirect(['login']);
                    } else {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->render('activate', [
                    'model' => $model,
                    'verification' => $verification,
        ]);
    }


    public function actionRestore($type=null, $uid=null, $code=null) {
        $model = new UserRestore();
        $model->scenario='new';
        if (!$type && !$uid && !$code && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $old=UserRestore::findOne(['uid'=>$model->uid, 'type'=>$model->type]);
            if ($old) {$old->delete();}
            $res=$model->authentication->typeModel->sendRestore($model);
            if ($res!==false) {
                Yii::$app->session->setFlash('flash.success',Yii::t('app/user','Password reset token sent to {type}:{uid}.',['type'=>$model->type,'uid'=>$model->uid]));
                return $this->redirect(['restore','type'=>$res->type,'uid'=>$res->uid]);
            }
            else {
                throw new \yii\web\ServerErrorHttpException(Yii::t('app', 'Failed to send a message.'));
            }
        }

        if ($type && $uid) {
            $model=  UserRestore::findOne(['uid'=>$uid, 'type'=>$type]);
            if (!$model) {
                throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
            }
            elseif ($code!==null) {
               if ($model->reset_token!=trim($code)) {
                   $model->addError('reset_token',Yii::t('app/user', 'Invalid reset token.'));
               }
               else {
                    $user=$model->authentication->user;
                    $user->scenario='password-reset';
                    if ($user->load(Yii::$app->request->post()) && $user->save()) {
                        if (Yii::$app->user->login($user, 0)) {
                            return $this->redirect(['u/default/index']);
                        }
                        else {
                            return $this->redirect(['user/login']);
                        }
                    }
                    return $this->render('restore-new-password', [
                        'model' => $model,
                        'user' => $user,
                        'code'=>$code,
                    ]);
               }
            }
            return $this->render('restore-code', [
                'model' => $model,
                'code'=>$code,
            ]);
        }


        return $this->render('restore', [
            'model' => $model,
        ]);
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
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    protected function findModelByUsername($username) {
        if (($model = User::findOne(['username' => $username])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
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
    protected function findAuthenicationModel($type, $uid) {
        if (($model = UserAuthentication::findOne(['type' => $type, 'uid' => $uid])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app/user', 'Authentication account {type}:{uid} not found!',['type'=>$type,'uid'=>$uid]));
        }
    }

}
