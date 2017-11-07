<?php

namespace app\modules\news\controllers;

use Yii;
use app\modules\news\models\News;
use app\modules\news\models\NewsSearch;
use app\modules\news\models\NewsType;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `news` module
 */
class DefaultController extends \app\common\web\DefaultController {

    public $rbacEnable=false;

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex($type=null)
    {
        $searchModel = new NewsSearch();
        $params=[];
        if ($type) {
            $typeModel=$this->findTypeByName($type);
            $params['NewsSearch']['typeName']=$type;
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

    public function actionView($id, $type=null) {
        $model=$this->findModel($id);
        if ($type===null && $model->type && $model->type->name) {
            $this->redirect(['view','id'=>$id,'type'=>$model->type->name]);
        }
        return $this->render('view',['model'=>$model]);
    }

    protected function findModel($id) {
        if (is_numeric($id)) {
            $model = News::findOne($id);
        }
        else {
             $model = News::find()->where(['uid'=>$id])->one();
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findTypeByName($type) {
        $model = NewsType::find()->where(['name'=>$type])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
