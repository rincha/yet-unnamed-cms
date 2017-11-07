<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%mail}}".
 *
 * @property integer $mail_id
 * @property string $from
 * @property string $to
 * @property string $reply_to
 * @property string $subject
 * @property string $text_body
 * @property string $html_body
 * @property string $data
 * @property string $mailer
 * @property string $uid
 * @property integer $status - bitmask
 * @property string $created_date
 * @property string $created_at
 * @property string $updated_at
 */
class Mail extends \yii\db\ActiveRecord {

    const STATUS_NEW=2;
    const STATUS_SENDED=4;
    const STATUS_ABORTED=8;
    const STATUS_ERROR=16;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%mail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['from', 'to', 'subject'], 'required'],
            [['text_body', 'html_body', 'data'], 'string'],
            [['status'], 'integer'],
            [['from', 'to', 'reply_to', 'subject'], 'string', 'max' => 255],
            [['from', 'to', 'reply_to'], 'email'],
            [['mailer'], 'string', 'max' => 32],
            [['uid'], 'string', 'max' => 64],
            [['uid'], 'unique', 'targetAttribute' => ['mailer', 'uid']],
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className() => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_date', 'created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getStatusText() {
        $res=[];
        foreach ($this->getStatusList() as $k=>$v) {
            if (($this->status&$k)==$k) {
                $res[]=$v;
            }
        }
        return implode(', ', $res);
    }

    public function getStatusList() {
        return [
            self::STATUS_NEW=>Yii::t('app/mail', 'New'),
            self::STATUS_SENDED=>Yii::t('app/mail', 'Sended'),
            self::STATUS_ABORTED=>Yii::t('app/mail', 'Aborted'),
            self::STATUS_ERROR=>Yii::t('app/mail', 'Error'),

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'mail_id' => Yii::t('app/mail', 'ID'),
            'from' => Yii::t('app/mail', 'From'),
            'to' => Yii::t('app/mail', 'To'),
            'reply_to' => Yii::t('app/mail', 'Reply to'),
            'subject' => Yii::t('app/mail', 'Subject'),
            'text_body' => Yii::t('app/mail', 'Text body'),
            'html_body' => Yii::t('app/mail', 'Html body'),
            'data' => Yii::t('app/mail', 'Data'),
            'mailer' => Yii::t('app/mail', 'Mailer'),
            'status' => Yii::t('app/mail', 'Status'),
            'created_date' => Yii::t('app/mail', 'Created date'),
            'created_at' => Yii::t('app/mail', 'Created at'),
            'updated_at' => Yii::t('app/mail', 'Updated at'),
        ];
    }

    /*public function afterSave($insert, $changedAttributes) {
        $res=parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            return $this->send();
        }
        else {
            return $res;
        }
    }*/

    public function send($markError=true) {
        $message=Yii::$app->mailer->compose()
        ->setFrom($this->from)
        ->setTo($this->to)
        ->setSubject($this->subject)
        ->setTextBody($this->text_body)
        ->setHtmlBody($this->html_body);
        if ($this->reply_to) {
            $message->replyTo=$this->reply_to;
        }
        $t=Yii::$app->db->beginTransaction();
        $this->status=($this->status|self::STATUS_SENDED) &~ self::STATUS_NEW;
        if ($this->save() && $message->send()) {
            $t->commit();
            return true;
        }
        else {
            $t->rollback();
            if ($markError) {
                $this->status=$this->status|self::STATUS_ERROR;
                $this->save();
            }
            return false;
        }
    }

    public function render($_file_, $_params_ = []) {
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        if (strpos($_file_,'@')!==0) {
            $_file_res_=Yii::getAlias('@app/mail/messages/') . $_file_ . '.php';
        }
        else {
            $_file_res_=Yii::getAlias($_file_. '.php');
        }
        require($_file_res_);
        return ob_get_clean();
    }

    /**
     * @return Mail
     */
    public static function create($to,$message,$subject=null,$replyTo=null, $from=null, $mailer=null,$uid=null) {
        $model=new Mail();
        $model->to=$to;
        if (is_array($message)) {
            $view=$message[0];
            $params=array_slice($message, 1);
            $message=$model->render($view,$params);
        }
        $model->html_body=$message;
        $model->text_body= strip_tags($message);
        $model->from=Yii::$app->params['adminEmail'];
        if (!$subject) {
            $model->subject=Yii::$app->params['siteName'];
        }
        else {
            $model->subject=$subject;
        }
        $model->reply_to=$replyTo;
        $model->status=self::STATUS_NEW;
        $model->mailer=$mailer;
        $model->uid=$uid;
        if ($model->save()) {
            return $model;
        }
        else {
            throw new \yii\base\Exception(strip_tags(\yii\helpers\Html::errorSummary($model)));
        }
    }

}
