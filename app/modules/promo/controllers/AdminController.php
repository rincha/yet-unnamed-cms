<?php

namespace app\modules\promo\controllers;

use Yii;
use app\modules\promo\models\Promo;
use app\modules\promo\models\PromoBlock;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Promo model.
 */
class AdminController extends \app\common\web\AdminController
{

    public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>'<i class="fa fa-square-o"></i><span class="text"> '.Yii::t('promo', 'Promo pages').'</span>', 'encode'=>false, 'url' => ['/promo/admin/index'], 'options'=>['title'=>Yii::t('promo', 'Promo')]]
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'block-delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Promo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Promo::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Promo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Promo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->promo_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Promo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->promo_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Promo model.
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
     * Finds the Promo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Promo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Promo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Lists all PromoBlock models.
     * @return mixed
     */
    public function actionBlockIndex($pid)
    {
        $promo=$this->findModel($pid);
        $dataProvider = new ActiveDataProvider([
            'query' => PromoBlock::find()->where(['promo_id'=>$pid]),
            'sort'=>[
                'defaultOrder'=>[
                    'sort_order'=>SORT_ASC,
                ],
            ]
        ]);

        return $this->render('block-index', [
            'dataProvider' => $dataProvider,
            'promo'=>$promo,
        ]);
    }

    /**
     * Creates a new PromoBlock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionBlockCreate($pid)
    {
        $promo=$this->findModel($pid);
        $model = new PromoBlock();
        $model->promo_id=$pid;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['block-index', 'pid' => $model->promo_id]);
        } else {
            return $this->render('block-create', [
                'model' => $model,
                'promo'=>$promo,
            ]);
        }
    }

    /**
     * Updates an existing PromoBlock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBlockUpdate($id)
    {
        $model = $this->findBlockModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['block-index', 'pid' => $model->promo_id]);
        } else {
            return $this->render('block-update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PromoBlock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBlockDelete($id)
    {
        $model=$this->findBlockModel($id);
        $model->delete();

        return $this->redirect(['block-index','pid'=>$model->promo_id]);
    }

    /**
     * Finds the PromoBlock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PromoBlock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findBlockModel($id)
    {
        if (($model = PromoBlock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
