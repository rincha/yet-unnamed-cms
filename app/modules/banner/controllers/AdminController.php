<?php

namespace app\modules\banner\controllers;

use Yii;
use app\modules\banner\models\Banner;
use app\modules\banner\models\BannerItem;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Banner model.
 */
class AdminController extends \app\common\web\AdminController {


    public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>'<i class="fa fa-file-image-o"></i> <span class="text">'.Yii::t('banner', 'Banners').'</span>', 'encode'=>false, 'url' => ['/banner/admin/index'], 'options'=>['title'=>Yii::t('banner', 'Banners')]]
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionLookup($term) {
        $models = Banner::find()->where(['like','name',$term])->limit(10)->all();
        $res=[];
        foreach ($models as $model) {
            $res[]=[
                'label'=>$model->name,
                'value'=>$model->banner_id,
            ];
        }
        return \yii\helpers\Json::encode($res);
    }

    /**
     * Lists all Banner models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Banner::find(),
        ]);
        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Banner model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model=$this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => BannerItem::find()->where(['banner_id'=>$model->banner_id]),
        ]);
        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,

        ]);
    }

    /**
     * Creates a new Banner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Banner();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->banner_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Banner model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->banner_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Banner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Creates a new Banner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionItemCreate($id) {
        $banner=$this->findModel($id);
        $model = new BannerItem();
        $model->banner_id=$id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->banner_id]);
        } else {
            return $this->render('item-create', [
                'banner' => $banner,
                'model' => $model,
            ]);
        }
    }

    public function actionItemUpdate($id) {
        $model = $this->findItemModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->banner_id]);
        } else {
            return $this->render('item-update', [
                'banner' => $model->banner,
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Banner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionItemDelete($id) {
        $model=$this->findItemModel($id);
        $model->delete();
        return $this->redirect(['view','id'=>$model->banner_id]);
    }

    /**
     * Finds the Banner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Banner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Banner::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the BannerItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BannerItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findItemModel($id) {
        if (($model = BannerItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
