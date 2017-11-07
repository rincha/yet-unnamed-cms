<?php

namespace app\modules\news\controllers;

use Yii;
use app\modules\news\models\News;
use app\modules\news\models\NewsSearch;
use app\modules\news\models\NewsType;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * AdminController implements the CRUD actions for News model.
 */
class AdminController extends \app\common\web\AdminController
{

     public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>'<i class="fa fa-newspaper-o"></i>'.  \yii\helpers\Html::tag('span',' '.Yii::t('news', 'News'),['class'=>'text']), 'encode'=>false, 'url' => ['/news/admin/index'], 'options'=>['title'=>Yii::t('news', 'News')]]
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
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
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
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        $model->date=  \Yii::$app->formatter->asDate(time());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->news_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->news_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing News model.
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
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Lists all NewsType models.
     * @return mixed
     */
    public function actionTypeIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => NewsType::find(),
        ]);

        return $this->render('type/index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new NewsType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionTypeCreate()
    {
        $model = new NewsType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['type-index', 'id' => $model->type_id]);
        } else {
            return $this->render('type/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NewsType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionTypeUpdate($id)
    {
        $model = $this->findModelType($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['type-index', 'id' => $model->type_id]);
        } else {
            return $this->render('type/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing NewsType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionTypeDelete($id)
    {
        $this->findModelType($id)->delete();

        return $this->redirect(['type-index']);
    }

    /**
     * Finds the NewsType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NewsType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelType($id)
    {
        if (($model = NewsType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
