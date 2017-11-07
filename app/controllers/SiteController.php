<?php

namespace app\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\validators\UrlValidator;

class SiteController extends \app\common\web\DefaultController {

    public $rbacEnable=false;

    public function actions() {
        return [
            'error' => [
                'class' => 'app\common\actions\ErrorAction',
            ],
            'captcha' => [
                'class' => 'app\common\actions\CaptchaActionRand',
                'fixedVerifyCode' => YII_ENV_TEST ? 'test' : null,
                'minLength'=>3,
                'maxLength'=>4,
                'testLimit'=>3,
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionReset($res=0) {
        if (Yii::$app->request->isPost) {
            $resetCookies=[
                Yii::$app->session->name,
                '_identity',
            ];
            Yii::$app->session->destroy();
            foreach ($resetCookies as $c) {
                setcookie($c, '', time()-3600*24, '/', $_SERVER['SERVER_NAME']);
                setcookie($c, '', time()-3600*24, '/');
            }
            return $this->redirect(['reset', 'res'=>1]);
        }
        return $this->render('reset',['res'=>$res]);
    }

    public function actionAway($url, $checksum) {
        $v=(new UrlValidator(['enableIDN'=>true]))->validate($url);
        if ($v!==true) {
            $url=urldecode($url);
        }
        $v=(new UrlValidator(['enableIDN'=>true]))->validate($url);
        if ($v!==true) {
            throw new BadRequestHttpException('Wrong URL');
        }
        if ($checksum!=hash_hmac("sha256", $url, Yii::$app->params['salt'])) {
            throw new BadRequestHttpException('Wrong checksum');
        }

        $purl=parse_url($url);
        $curl=parse_url(Yii::$app->urlManager->hostInfo);
        $curl['host']='detivradost.ru';
        $n=0;
        while ($n<10 && $purl['host']==$curl['host'] && $purl['path']=='/site/away/') {
            $turl=urldecode($purl['query']);
            parse_str($turl,$tparams);
            if (isset($tparams['url'])) {
                $purl=parse_url($tparams['url']);
                $url=$tparams['url'];
            }
            $n++;
        }
        if ($n>0 && isset($purl['query'])) {
            parse_str($purl['query'],$tparams);
            if (isset($tparams['url'])) {
                $purl=parse_url($tparams['url']);
                $url=$tparams['url'];
            }
        }
        if ($purl['host']==$curl['host']) {
            return $this->redirect($url);
        }

        return $this->render('away',['url'=>$url]);
    }

}
