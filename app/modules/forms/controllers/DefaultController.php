<?php

namespace app\modules\forms\controllers;

use Yii;
use app\modules\forms\models\Form;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;

class DefaultController extends \app\common\web\DefaultController {

    public $rbacEnable=false;

    public function behaviors()
    {
        return parent::behaviors()+[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'api-send' => ['post'],
                ],
            ],
        ];
    }

    public function actionView($id)
    {
        $form=$this->findModel($id);

        $model=new \app\modules\forms\models\FormSend();
        $model->form=$form;

        if ($model->load(Yii::$app->request->post()) && !Yii::$app->request->post('loadOnly')) {
            if ($model->send()) {
                Yii::$app->session->setFlash('flash.success','Форма успешно отправлена.');
                return $this->redirect(['view','id'=>$id]);
            }
            else {
                Yii::$app->session->setFlash('flash.error','Не удалось отправить форму. Проверьте заполнение.');
            }
        }

        return $this->render('view', [
            'model' => $model,
            'form' => $form,
        ]);
    }

    public function actionApiSend($id) {
        $form=$this->findModel($id);
        $model=new \app\modules\forms\models\FormSend();
        $model->form=$form;
        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            return '<div class="alert alert-success">Форма успешно отправлена!</div>';
        }
        else {
            throw new BadRequestHttpException(\yii\helpers\Html::errorSummary($model));
        }
    }

    protected function findModel($id)
    {
        if (is_numeric($id)) {
            $model = Form::findOne($id);
        }
        else {
            $model = Form::findOne(['name'=>$id]);
        }
        if ($model !== null && $model->status) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
