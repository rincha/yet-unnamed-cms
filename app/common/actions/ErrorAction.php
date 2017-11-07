<?php

namespace app\common\actions;

use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\base\UserException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class ErrorAction extends Action
{
    /**
     * @var string the view file to be rendered. If not set, it will take the value of [[id]].
     * That means, if you name the action as "error" in "SiteController", then the view name
     * would be "error", and the corresponding view file would be "views/site/error.php".
     */
    public $view;
    /**
     * @var string the name of the error when the exception name cannot be determined.
     * Defaults to "Error".
     */
    public $defaultName;
    /**
     * @var string the message to be displayed when the exception message contains sensitive information.
     * Defaults to "An internal server error occurred.".
     */
    public $defaultMessage;


    /**
     * Runs the action
     *
     * @return string result content
     */
    public function run()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
        }
        $additional_message=null;

        if ($exception instanceof ForbiddenHttpException && Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
            \Yii::$app->user->returnUrl= \Yii::$app->request->url;
            $temp=new \app\models\LoginForm();
            $temp->scenario='login';
            $additional_message=$this->controller->renderPartial('@app/views/user/login-form.php', [
                'model'=>$temp,
            ]);
            $additional_message='<div class="panel panel-default"><div class="panel-heading">'.Yii::t('app', 'You need to login').'</div><div class="panel-body">'.$additional_message.'</div></div>';
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        if ($exception instanceof Exception) {
            $name = Yii::t('app', $exception->getName());
        } else {
            $name = $this->defaultName ?: Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)!";
        }

        if ($exception instanceof UserException) {
            $message = Yii::t('app', $exception->getMessage());
        } else {
            $message = $this->defaultMessage ?: Yii::t('yii', 'An internal server error occurred.');
        }

        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            return $this->controller->render($this->view ?: $this->id, [
                'name' => $name,
                'message' => $message,
                'exception' => $exception,
                'additional_message'=>$additional_message,
            ]);
        }
    }
}
