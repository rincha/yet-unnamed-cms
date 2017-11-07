<?php

namespace app\modules\post\controllers;

use Yii;
use app\modules\post\models\PostSearch;
use app\modules\post\models\Post;
use app\modules\post\models\Comment;
use app\modules\post\models\CommentSearch;
use app\models\UserSearch;
use app\modules\post\Module;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `post` module
 */
class DefaultController extends \app\common\web\DefaultController {

    public $rbacEnable=false;

    public function actionIndex() {
        $searchModel = new PostSearch();
        $searchModel->scenario= PostSearch::SCENARIO_USER;
        $searchModel->status=$this->module->displayStatus;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        $model=$this->findModel($id);
        $comment=new Comment();
        $comment->post_id=$model->post_id;
        if ($this->commentSave($comment)) {
            return $this->redirect(['view','id'=>$id]);
        }
        else {
            $commentSearch=new CommentSearch();
            $commentSearch->post_id=$model->post_id;
            if (!$model->isCanUpdate()) {
                $commentSearch->status=$this->module->commentDisplayStatus;
            }
            $commentSearch->parent_id=false;
            $commentsDp=$commentSearch->search([]);
            Comment::queryAddHasBranch($commentsDp->query);
            $data=[
                'model' => $model,
                'comment'=>$comment,
                'commentsDp'=>$commentsDp,
            ];
            return Yii::$app->request->isPjax?
                    $this->renderAjax('view', $data):
                    $this->render('view', $data);
        }
    }

    private function commentSave(Comment $model) {
        if (Yii::$app->user->isGuest) {
            $model->scenario=Comment::SCENARIO_GUEST;
            $model->status=$this->module->commentGuestStatus;
        }
        else {
            $model->scenario=Comment::SCENARIO_USER;
            $model->status=$this->module->commentUserStatus;
            $model->author_id=Yii::$app->user->id;
            $model->author_nickname=Yii::$app->user->identity->username;
            $model->author_email=isset(Yii::$app->user->identity->authenications['email'])?Yii::$app->user->identity->authenications['email']->uid:null;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (in_array($model->status, $this->module->commentDisplayStatus)) {
                $message=Yii::t('post', 'The comment was successfully published.');
            }
            else {
                $message=Yii::t('post', 'The comment was successfully added to the publication queue.');
            }
            Yii::$app->session->setFlash('flash.success',$message);
            return true;
        }
        else {
            return false;
        }
    }

    public function actionAuthors() {
        $searchModel=new UserSearch();
        $searchModel->_auth_assigment_role=Module::ROLE_AUTHOR;
        $searchModel->status= UserSearch::STATUS_ACTIVE;
        $searchModel->scenario='none';
        $dataProvider = $searchModel->search([]);
        return $this->render('authors', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id) {
        $model = Post::findOne((preg_match('/\d+/ui', $id)&&$id!=null)?$id:['uid'=>$id]);
        if ($model && in_array($model->status, $this->module->displayStatus)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findAuthor($id) {
        if (($model = User::findOne($id)) !== null && $model->status==User::STATUS_ACTIVE && isset($model->authItems[Module::ROLE_AUTHOR])) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
