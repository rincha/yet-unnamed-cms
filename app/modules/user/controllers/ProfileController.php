<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\models\User;

/**
 * ProfileController implements the CRUD actions for ProfilePerson model.
 */
class ProfileController extends \app\common\web\DefaultController {


    public function actionIndex() {
        return $this->render('index', [
            'model' => $this->findModel(Yii::$app->user->id),
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
