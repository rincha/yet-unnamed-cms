<?php

namespace app\modules\promo\controllers;
use app\modules\promo\models\Promo;
use yii\web\NotFoundHttpException;

class DefaultController extends \app\common\web\DefaultController
{
    public $layout = '@app/modules/promo/views/layouts/promo';
    public $rbacEnable=false;

    public function actionView($id)
    {
        $model=$this->findModel($id);
        return $this->render('view',['model'=>$model]);
    }

    protected function findModel($id) {
        if (is_numeric($id)) {
            $model = Promo::findOne($id);
        }
        else {
             $model = Promo::find()->where(['uid'=>$id])->one();
        }
        if ($model !== null && $model->status==Promo::STATUS_ENABLED) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
