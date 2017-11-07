<?php

namespace app\modules\files\controllers;

use Yii;
use app\modules\files\models\Folder;
use app\modules\files\models\File;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class DefaultController extends \app\common\web\DefaultController {

    public $rbacEnable=false;

    public function actionView($id) {
        $folder = $this->findModel($id);
        $query=File::find()->where(['folder_id'=>$folder->folder_id])->orderBy('name ASC');
        if ($folder->type== Folder::TYPE_GALLERY) {
            $query->andWhere(['in','ext',['jpg','png','jpeg']]);
        }
        $dataProvider=new ActiveDataProvider([
            'query'=>$query,
        ]);
        return $this->render('view-'.$folder->type, [
            'folder' => $folder,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Folder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Folder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Folder::findOne($id)) !== null && $model->type) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.0');
        }
    }

    protected function findModelFile($id) {
        if (($model = File::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
