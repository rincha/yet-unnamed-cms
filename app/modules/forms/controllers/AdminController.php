<?php

namespace app\modules\forms\controllers;

use Yii;
use app\modules\forms\models\Form;
use app\modules\forms\models\FormSearch;
use app\modules\forms\models\FormField;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Form model.
 */
class AdminController extends \app\common\web\AdminController {

    public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>'<i class="glyphicon glyphicon-list-alt"></i> <span class="text">'.Yii::t('forms', 'Forms').'</span>', 'encode'=>false, 'url' => ['/forms/admin/index'], 'options'=>['title'=>Yii::t('forms', 'Forms')]]
            ]
        ];
    }

    public function behaviors() {
        return parent::behaviors()+[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Form models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new FormSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Form model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Form model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Form();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->form_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Form model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->form_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Form model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Form model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Form the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Form::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionFieldCreate($id) {
        $form = $this->findModel($id);
        $model = new FormField();

        if ($model->load(Yii::$app->request->post())) {
            $model->form_id = $form->form_id;
            if ($model->save())
                return $this->redirect(['view', 'id' => $model->form_id]);
        } else {
            return $this->render('field_create', [
                        'form' => $form,
                        'model' => $model,
            ]);
        }
    }

    public function actionFieldUpdate($id) {
        $model = $this->findFieldModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->form_id]);
        } else {
            return $this->render('field_update', [
                        'model' => $model,
                        'form' => $model->form,
            ]);
        }
    }

    public function actionFieldDelete($id) {
        $model = $this->findFieldModel($id);
        $model->delete();

        return $this->redirect(['view', 'id' => $model->form_id]);
    }

    protected function findFieldModel($id) {
        if (($model = FormField::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
