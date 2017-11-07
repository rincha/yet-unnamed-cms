<?php

namespace app\modules\post\controllers;

use Yii;
use app\modules\post\models\Post;
use app\modules\post\models\PostSearch;
use app\modules\post\models\Comment;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Author controller for the `post` module
 */
class AuthorController extends \app\common\web\DefaultController {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'comment-status' => ['POST'],
                    'publish' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new PostSearch();
        $searchModel->scenario= PostSearch::SCENARIO_OWNER;
        $searchModel->author_id=Yii::$app->user->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        $model = $this->findModel($id);
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    public function actionCreate() {
        $model = new Post();
        $model->scenario=Post::SCENARIO_OWNER;
        $model->status = Post::STATUS_DRAFT;
        $model->author_id = Yii::$app->user->id;
        if ($model->load(Yii::$app->request->post())) {
            $model->images_add = UploadedFile::getInstances($model, 'images_add');
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->save() && $model->saveImages() !== false) {
                $transaction->commit();
                Yii::$app->session->setFlash('flash.success',Yii::t('app', 'Operation completed successfully'));
                return $this->redirect(['view', 'id' => $model->post_id]);
            } else {
                $transaction->rollBack();
            }
        }
        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario=Yii::$app->user->can('PostAdmin')?Post::SCENARIO_ADMIN:Post::SCENARIO_OWNER;
        if ($model->load(Yii::$app->request->post())) {
            $model->images_add = UploadedFile::getInstances($model, 'images_add');
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->save() && $model->saveImages() !== false) {
                $transaction->commit();
                Yii::$app->session->setFlash('flash.success',Yii::t('app', 'Operation completed successfully'));
                return $this->redirect(['view', 'id' => $model->post_id]);
            } else {
                $transaction->rollBack();
            }
        }
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('flash.success',Yii::t('app', 'Operation completed successfully'));
        }
        else {
            Yii::$app->session->setFlash('flash.error',Yii::t('app', 'Operation failed'));
        }
        return $this->redirect(['index']);
    }

    public function actionPublish($id,$publish) {
        $model = $this->findModel($id);
        $model->scenario=Post::SCENARIO_SYSTEM;
        $model->status=$publish?Post::STATUS_NEW:Post::STATUS_DRAFT;
        if ($model->save()) {
            Yii::$app->session->setFlash('flash.success',Yii::t('app', 'Operation completed successfully'));
            return $this->redirect(['view', 'id' => $model->post_id]);
        }
        else {
            throw new BadRequestHttpException(Yii::t('post', 'Could not change status.'));
        }
    }

    public function actionCommentStatus($id,$status) {
        $model = $this->findCommentModel($id);
        $model->scenario= Comment::SCENARIO_POST_OWNER;
        $model->status=$status;
        if ($model->save()) {
            return 'OK';
        }
        else {
            throw new BadRequestHttpException(Yii::t('post', 'Could not change status.'));
        }
    }

    /**
     * @return Post
     */
    protected function findModel($id) {
        if (($model = Post::findOne($id)) !== null) {
            if (!$model->isCanUpdate()) {
                throw new ForbiddenHttpException('Permission denied');
            } else {
                return $model;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return Comment
     */
    protected function findCommentModel($id) {
        if (($model = Comment::findOne($id)) !== null) {
            if (!$model->post->isCanUpdate()) {
                throw new ForbiddenHttpException('Permission denied');
            } else {
                return $model;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
