<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\files\models\Folder;
use app\modules\files\models\FolderSearch;
use app\modules\files\models\File;
use app\modules\files\models\FileSearch;
use app\common\web\AdminController;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Folder model.
 */
class FilesController extends AdminController {

    public function behaviors() {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'delete-file' => ['post'],
                    'api-create-folder' => ['post'],
                    'api-create-file' => ['post'],
                    'api-delete-file' => ['post'],
                    'api-delte-folder' => ['post'],
                    'api-update-file' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Folder models.
     * @return mixed
     */
    public function actionIndex($parent_id = null) {
        if ($parent_id)
            $parent = $this->findModel($parent_id);
        else
            $parent = null;
        $searchModel = new FolderSearch();
        $searchModel->parent_id = $parent_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'parent' => $parent,
                    'parent_id' => $parent_id,
        ]);
    }

    /**
     * Creates a new Folder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Folder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->folder_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Folder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->folder_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Folder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model=$this->findModel($id);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Folder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Folder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Folder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionView($id) {
        $folder = $this->findModel($id);

        $searchModel = new FileSearch();
        $searchModel->folder_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('file_index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'folder' => $folder,
        ]);
    }

    public function actionCreateFile($folder_id) {
        $folder = $this->findModel($folder_id);
        $model = new File();
        $model->folder_id = $folder_id;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->folder_id]);
            }
        }

        return $this->render('file_create', [
                    'model' => $model,
                    'folder' => $folder,
        ]);
    }

    public function actionUpdateFile($id) {
        $model = $this->findModelFile($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->folder_id]);
            }
        }

        return $this->render('file_update', [
                    'model' => $model,
        ]);
    }

    public function actionDeleteFile($id) {
        $model = $this->findModelFile($id);
        $model->delete();

        return $this->redirect(['view', 'id' => $model->folder_id]);
    }

    protected function findModelFile($id) {
        if (($model = File::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function apiGetFolders($id) {
        $models = Folder::getListAll($id, 1);
        $res = [];
        foreach ($models as $model) {
            $res[] = [
                'id' => $model->folder_id,
                'title' => $model->name,
                'has_children' => $model->has_children,
                'children' => $model->has_children?$this->apiGetFolders($model->folder_id):[],
                'parent_id' => $model->parent_id,
            ];
        }
        return $res;
    }

    public function actionApiGetFolders($id = null) {
        return json_encode($this->apiGetFolders($id));
    }

    public function actionApiGetFiles($id) {
        $models = File::find()->where(['folder_id' => $id])->orderBy('name ASC')->all();
        $res = [];
        foreach ($models as $model) {
            $size=0;
            $mime='';
            if (file_exists($model->FullFileName)) {
                $size=filesize($model->FullFileName);
                $mime=\yii\helpers\FileHelper::getMimeType($model->FullFileName);
            }
            $res[] = [
                'id' => $model->file_id,
                'title' => $model->name,
                'ext' => $model->ext,
                'file' => $model->url,
                'size'=>$size,
                'mime'=>$model->type,
                'type'=>$model->fileType,
                'w'=>$model->imageInfo?$model->imageInfo['width']:'0',
                'h'=>$model->imageInfo?$model->imageInfo['height']:'0',
                'params' => [],
            ];
        }
        return json_encode($res);
    }

    public function actionApiCreateFolder() {
        $model = new Folder();
        $res = [];
        if (isset($_POST['Folder'])) {
            $data = [
                'name' => \yii\helpers\ArrayHelper::getValue($_POST['Folder'], 'title'),
                'pathname' => \yii\helpers\ArrayHelper::getValue($_POST['Folder'], 'title'),
                'description' => \yii\helpers\ArrayHelper::getValue($_POST['Folder'], 'description'),
                'parent_id' => \yii\helpers\ArrayHelper::getValue($_POST['Folder'], 'parent_id'),
            ];

            $model->load(['Folder'=>$data]);
            if ($model->save()) {
                $res['success'] = true;
            } else {
                $res['error'] = $model->errors;
            }
        }
        return json_encode($res);
    }

    public function actionApiCreateFile($id, $inframe = false) {
        $folder = Folder::findOne($id);
        if (!$folder)
            throw new NotFoundHttpException('The requested page does not exist.');
        $files = \yii\web\UploadedFile::getInstancesByName('Filedata');
        $res = [];
        $n = 0;
        foreach ($files as $file) {
            $model = new File();
            $model->folder_id = $folder->folder_id;
            $model->new_file = $file;
            if ($model->save()) {
                $res[$n]['success'] = 'Файл ' . htmlspecialchars($model->name) . ' успешно загружен.';
            } else {
                $res[$n]['error'] = $model->errors;
            }
            $n++;
        }
        if (!$inframe)
            return json_encode($res);
        else {
            $ret = [];
            foreach ($res as $n => $types) {
                foreach ($types as $type => $mes) {
                    $ret[] = \yii\helpers\Html::tag('div', \yii\helpers\Html::encode($mes), ['class' => 'alert alert-' . $type]);
                }
            }
            return implode(' ', $ret);
        }
    }

    public function actionApiDeleteFile($id) {
        $model = File::findOne($id);
        if (!$model)
            throw new NotFoundHttpException('The requested page does not exist.');
        $res = [];
        if ($model->delete()) {
            $res['success'] = true;
        } else
            $res['error'] = 'ОШИБКА!';
        return json_encode($res);
    }

    public function actionApiDeleteFolder($id) {
        $model = Folder::findOne($id);
        if (!$model)
            throw new NotFoundHttpException('The requested page does not exist.');
        $res = [];
        if ($model->delete()) {
            $res['success'] = true;
        } else
            $res['error'] = 'ОШИБКА!';
        return json_encode($res);
    }

    public function actionApiUpdateFile($id) {
        $model = File::findOne($id);
        if (!$model)
            throw new NotFoundHttpException('The requested page does not exist.');
        $res = [];
        if (isset($_POST['File'])) {
            $data = [
                'name' => \yii\helpers\ArrayHelper::getValue($_POST['File'], 'title', null),
            ];
            $model->attributes = $data;
            if ($model->save()) {
                $res['success'] = true;
            } else {
                $res['error'] = $model->errors;
            }
        } else {

        }
        return json_encode($res);
    }

}
