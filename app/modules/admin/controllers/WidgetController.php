<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Widget;
use app\models\WidgetSearch;
use app\common\web\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WidgetController implements the CRUD actions for Widget model.
 */
class WidgetController extends AdminController
{
    
    public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>yii\bootstrap\Html::icon('th').  \yii\helpers\Html::tag('span',' '.Yii::t('app/widgets', 'Widgets'),['class'=>'text']), 'encode'=>false, 'url' => ['/admin/widget/index'], 'options'=>['title'=>Yii::t('app/widgets', 'Widgets')]]
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return parent::behaviors()+[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Widget models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WidgetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Widget model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Widget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type=null)
    {
        if ($type===null) {
            return $this->render('select-type', ['types'=>Yii::$app->params['widgets']['items']]);
        }
        else {
            $model = new Widget();
            if (!isset(Yii::$app->params['widgets']['items'][$type])) {
                throw new \yii\web\BadRequestHttpException();
            }
            $model->type=$type;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->widget_id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Updates an existing Widget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->widget_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Widget model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Widget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Widget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Widget::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
