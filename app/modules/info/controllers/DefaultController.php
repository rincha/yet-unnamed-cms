<?php

namespace app\modules\info\controllers;

use Yii;
use app\modules\info\models\Info;
use app\modules\info\models\InfoSearch;
use app\modules\info\models\Type;
use yii\web\NotFoundHttpException;

class DefaultController extends \app\common\web\DefaultController {

    public $rbacEnable=false;

    public function actionIndex($type=null)
    {
        if (!Yii::$app->getModule('info')->enableIndexAction) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        elseif (!$type && !Yii::$app->getModule('info')->enableIndexActionWithoutType) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $searchModel = new InfoSearch();
        $params=[];
        if ($type) {
            $typeModel=$this->findTypeByName($type);
            $params['InfoSearch']['typeName']=$type;
        }
        else {
            $typeModel=null;
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'typeModel'=>$typeModel,
        ]);
    }

    public function actionView($id) {
        return $this->render('view',['model'=>  $this->findModel($id)]);
    }

    protected function findModel($id) {
        if (is_numeric($id)) {
            $model = Info::findOne($id);
        }
        else {
             $model = Info::find()->where(['uid'=>$id])->one();
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findTypeByName($type) {
        $model = Type::find()->where(['name'=>$type])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
