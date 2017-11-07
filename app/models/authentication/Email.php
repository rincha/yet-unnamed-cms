<?php

namespace app\models\authentication;

use Yii;

class Email extends BaseType {

    protected $owner;

    public static function allowedMethods() {
        return ['validate', 'sendMessage', 'sendRestore'];
    }

    public function sendConfirm() {
        $this->owner->verification = Yii::$app->security->generateRandomString(32);
        $this->owner->verification_expire = new yii\db\Expression('DATE_ADD(now(), INTERVAL 3 DAY)');

        $flag_send = Yii::$app->mailer->compose(
                        [
                    'html' => '@app/views/user/email/send_confirm_html',
                    'text' => '@app/views/user/email/send_confirm_text',
                        ], [
                    'authentication' => $this->owner,
                        ]
                )
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($this->owner->uid)
                ->setSubject(Yii::$app->params['siteName'] . ': ' . Yii::t('app/user', 'Account activation'))
                ->send();

        if (!$flag_send) {
            Yii::$app->session->setFlash('flash.error', Yii::t('app', 'Failed to send a message.'));
        } else {
            Yii::$app->session->setFlash('flash.success', Yii::t('app/user', 'The message with the activation code sent to {uid}.', ['uid' => $this->owner->uid]));
        }

        if ($this->owner->save())
            return ['redirect' => ['/user/activate', 'uid' => $this->owner->uid, 'type' => 'email']];
        else
            return ['errors' => $this->owner->errors];
    }

    public function validate() {
        $validator = new \yii\validators\EmailValidator([
            'pattern'=>"/^[a-zA-Z0-9!#$%&'*+\\/=?^_`{|}~-]+(\.[.a-zA-Z0-9!#$%&'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/",
        ]);
        $res = $validator->validateAttribute($this->owner, 'uid');
        if ($res) {
            return true;
        } else {
            return $this->owner->errors;
        }
    }

    public function sendMessage($message, $type = null, Array $params = []) {
        /* @var $sendmessage yii\swiftmailer\Message */
        $sendmessage = Yii::$app->mailer->compose();

        $body = [];
        if (is_array($message) && isset($message['html']) && isset($message['text'])) {
            $sendmessage->htmlBody = $message['html'];
            $sendmessage->textBody = $message['text'];
        } elseif (is_string($message)) {
            $sendmessage->htmlBody = $message;
            $sendmessage->textBody = strip_tags($message);
        } else {
            throw new Exception('@param $message must be string or array with elements: html, text');
        }
        if (isset($params['replyTo'])) {
            $sendmessage->replyTo = $params['replyTo'];
        }
        if (isset($params['from'])) {
            $sendmessage->from = $params['from'];
        } else {
            $sendmessage->from = Yii::$app->params['adminEmail'];
        }
        if (isset($params['subject'])) {
            $sendmessage->subject = $params['subject'];
        } else {
            $sendmessage->subject = Yii::$app->params['siteName'] . ': ' . Yii::t('app', 'Message');
        }
        return $sendmessage->setTo($this->owner->uid)->send();
    }

    /**
     * Send message for restet password.
     * @param app\models\UserRestore $restore
     * @return app\models\UserRestore|false
     */
    public function sendRestore(\app\models\UserRestore $restore) {
        $restore->scenario='send';
        $restore->reset_token=Yii::$app->security->generateRandomString(32);
        $restore->expire=  new \yii\db\Expression('DATE_ADD(now(),INTERVAL 1 DAY)');
        if ($restore->save()) {
            $flag=Yii::$app->mailer->compose([
                        'html' => '@app/views/user/email/send_restore_html',
                        'text' => '@app/views/user/email/send_restore_text',
                    ], [
                        'authentication' => $this->owner,
                        'restore'=>$restore,
                    ]
                )
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($this->owner->uid)
                ->setSubject(Yii::$app->params['siteName'] . ': ' . Yii::t('app/user', 'Restore access'))
                ->send();
            if (!$flag) {
                $restore->delete();
                return false;
            }
            else {
                return $restore;
            }
        }
        else {
            Yii::trace(strip_tags(\yii\helpers\Html::errorSummary($restore)));
            return false;
        }
    }

}

?>
