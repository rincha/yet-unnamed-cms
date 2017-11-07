<?php

namespace app\modules\menu\controllers;

use Yii;
use app\modules\menu\models\Menu;
use app\modules\menu\models\MenuSearch;
use app\modules\menu\models\MenuItem;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Menu model.
 */
class AdminController extends \app\common\web\AdminController {

    public static function apiAdmin() {
        return [
            'menu'=>[
                ['label'=>'<i class="glyphicon glyphicon-menu-hamburger"></i> <span class="text">'.Yii::t('menu', 'Menu').'</span>', 'encode'=>false, 'url' => ['/menu/admin/index'], 'options'=>['title'=>Yii::t('menu', 'Menu')]]
            ]
        ];
    }

    public function behaviors() {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionLookup($term) {
        $models = Menu::find()->where(['like','name',$term])->limit(10)->all();
        $res=[];
        foreach ($models as $model) {
            $res[]=[
                'label'=>$model->name,
                'value'=>$model->key?$model->key:$model->menu_id,
            ];
        }
        return \yii\helpers\Json::encode($res);
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Menu();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->menu_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        //$model->options=$model->getOp
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->menu_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelItem($id) {
        if (($model = MenuItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * MenuItems models manage.
     * @return mixed
     */
    public function actionItems($id) {
        $menu = $this->findModel($id);

        return $this->render('items', [
                    'menu' => $menu,
        ]);
    }

    /**
     * Creates a new MenuItem model.
     * If creation is successful, the browser will be redirected to the 'items' page.
     * @return mixed
     */
    public function actionCreateItem($id) {
        $menu = $this->findModel($id);

        $model = new MenuItem();
        $model->scenario = 'create';
        $model->menu_id = $menu->menu_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['items', 'id' => $model->menu_id]);
        } else {
            return $this->render('cuItem', [
                        'menu' => $menu,
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates a MenuItem model.
     * If creation is successful, the browser will be redirected to the 'items' page.
     * @return mixed
     */
    public function actionUpdateItem($id) {

        $model = $this->findModelItem($id);
        //$model->scenario='update';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['items', 'id' => $model->menu_id]);
        } else {
            return $this->render('cuItem', [
                        'menu' => $model->menu,
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MenuItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteItem($id) {
        $model = $this->findModelItem($id);
        $model->delete();

        return $this->redirect(['items', 'id' => $model->menu_id]);
    }

}
