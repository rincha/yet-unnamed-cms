<?php

namespace app\models\authentication;

use Yii;

class Phone extends BaseType {

     /*
      * @property app\models\UserAuthentication $owner
      */

    protected $owner;

    public static function allowedMethods(){
        return ['validate'];
    }

    public function sendConfirm() {

        $this->owner->verification = str_pad(rand(0, 99999),5,'0',STR_PAD_LEFT);
        $this->owner->verification_expire = new yii\db\Expression('DATE_ADD(now(), INTERVAL 15 MINUTE)');

        if ($this->owner->save()) {
            $gate_classname=$this->owner->typeConfig['gate'];
            $gate=new $gate_classname($this->owner->typeConfig['gate_config']);
            $message=Yii::$app->view->renderFile('@app/views/user/phone/send_confirm_text.php',['authentication'=>$this->owner]);
            $flag_send=$gate->sendSMS($this->owner->uid,$message);
            if (!$flag_send) {
                Yii::$app->session->setFlash('flash.error', Yii::t('app','Failed to send a message.'));
            }
            return ['redirect' => ['activate', 'uid' => $this->owner->uid, 'type' => 'phone']];
        }
        else {
            return ['errors' => $this->owner->errors];
        }
    }

    public function validate(){
        $validator = new \yii\validators\RegularExpressionValidator([
            'pattern'=>'/^[0-9]{11}$/ui',
            //'message'=>'',
        ]);
        $res=$validator->validateAttribute($this->owner,'uid');
        if ($res) {
            return true;
        } else {
            return $this->owner->errors;
        }
    }

}

?>
