<?php

namespace app\modules\info\controllers;

use Yii;
use app\modules\info\models\Info;
use app\modules\info\models\InfoSearch;
use app\modules\info\models\Type;
use app\modules\info\models\RelationType;
use app\modules\info\models\Relation;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Info model.
 */
class AdminController extends \app\common\web\AdminController {

    public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>'<i class="glyphicon glyphicon-file"></i> <span class="text">'.Yii::t('info', 'Information materials').'</span>', 'encode'=>false, 'url' => ['/info/admin/index'], 'options'=>['title'=>Yii::t('info', 'Information materials')]]
            ]
        ];
    }

    public function behaviors() {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'relation-delete' => ['post'],
                    'relation-update' => ['post'],
                    'type-delete' => ['post'],
                    'relation-type-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Info models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new InfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Info model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Info model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Info();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->info_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Info model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->date = \Yii::$app->formatter->asDate(time());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->info_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Info model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionRelations($id) {
        $model = $this->findModel($id);
        $types = RelationType::find()->all();
        $relation = new Relation();
        $relation->master_id = $model->info_id;
        if ($relation->load(Yii::$app->request->post()) && $relation->save()) {
            return $this->redirect(['relations', 'id' => $id]);
        }
        return $this->render('relations', [
                    'model' => $model,
                    'types' => $types,
                    'relation' => $relation,
        ]);
    }

    public function actionRelationUpdate($id) {
        $model = $this->findRelationModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                return "OK";
            }
            else {
                return $this->redirect(['relations', 'id' => $model->master_id]);
            }
        }
        else {
            throw new \yii\web\BadRequestHttpException();
        }

    }

    /**
     * Deletes an existing Relation model.
     * If deletion is successful, the browser will be redirected to the 'relations' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRelationDelete($id) {
        $model = $this->findRelationModel($id);
        $model->delete();
        return $this->redirect(['relations', 'id' => $model->master_id]);
    }

    public function actionLookup($term) {
        $res = [];
        $models = Info::find()->where(['like', 'name', $term])->limit(10)->orderBy('name')->all();
        foreach ($models as $model) {
            $res[] = ['label' => $model->name, 'value' => $model->info_id];
        }
        return \yii\helpers\Json::encode($res);
    }

    /**
     * Finds the Info model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Info the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Info::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findRelationModel($id) {
        if (($model = Relation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Lists all Type models.
     * @return mixed
     */
    public function actionTypeIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Type::find(),
        ]);

        return $this->render('type/index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Type model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionTypeCreate() {
        $model = new Type();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['type-index', 'id' => $model->type_id]);
        } else {
            return $this->render('type/create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Type model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionTypeUpdate($id) {
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
     * Deletes an existing Type model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionTypeDelete($id) {
        $this->findModelType($id)->delete();

        return $this->redirect(['type-index']);
    }

    /**
     * Finds the Type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelType($id) {
        if (($model = Type::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Lists all RelationType models.
     * @return mixed
     */
    public function actionRelationTypeIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => RelationType::find(),
        ]);

        return $this->render('relation-type/index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new RelationType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRelationTypeCreate() {
        $model = new RelationType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['relation-type-index', 'id' => $model->type_id]);
        } else {
            return $this->render('relation-type/create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RelationType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRelationTypeUpdate($id) {
        $model = $this->findRelationTypeModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['relation-type-index', 'id' => $model->type_id]);
        } else {
            return $this->render('relation-type/update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RelationType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRelationTypeDelete($id) {
        $this->findRelationTypeModel($id)->delete();

        return $this->redirect(['relation-type-index']);
    }

    /**
     * Finds the RelationType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RelationType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findRelationTypeModel($id) {
        if (($model = RelationType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
