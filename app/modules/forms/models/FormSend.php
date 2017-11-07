<?php
namespace app\modules\forms\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use app\modules\forms\components\SmsMainsms;
use app\common\helpers\AppHelper;


class FormSend extends Model {

    private $_form;
    private $_rules=[];
    private $_labels=[];
    private $_hints=[];

    public function setForm($value) {
        if (get_class($value)=='app\modules\forms\models\Form') {
            $this->_form=$value;
            foreach ($this->_form->fields as $field) {
                if ($field->name!='_form') {
                    $this->{$field->name}=null;
                    $this->_labels[$field->name]=$field->title;
                    $this->_hints[$field->name]=$field->tip;
                    if ($field->required) {
                        $this->_rules[]=[$field->name,'required'];
                    }
                    $rules=$field->type['rules'];
                    foreach ($rules as $rule) {
                        array_unshift($rule,$field->name);
                        foreach ($rule as $key=>$val) {
                            if (is_callable($val)) {
                                $rule[$key]=$val($field);
                            }
                        }
                        $this->_rules[]=$rule;
                    }

                    //$this->_rules[]
                }
            }
        }
        else {
            throw new Exception('form attribute must be instance of app\modules\forms\models\Form');
        }
    }

    public function rules() {
        return $this->_rules;
    }

    public function attributeLabels() {
        return $this->_labels;
    }

    public function attributeHints() {
        return $this->_hints;
    }

    public function getForm() {
        return $this->_form;
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
        elseif (true) {
            $this->{$name}=$value;
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    public function send() {
        if ($this->validate()) {
            $flag_send=false;
            $message='Пользователь отправил форму: '.$this->form->title."\r\n\r\n-----------------\r\n";
            foreach ($this->form->fields as $field) {
                $message .= $field->title.': '.($this->{$field->name})."\r\n";
            }
            $message.="-----------------\r\n".'Время: '.date('Y-m-d H:i:s');
            $message.="\r\nIP-адрес: ".$_SERVER['REMOTE_ADDR'];
            $message.="\r\nUser-Agent: ".$_SERVER['HTTP_USER_AGENT'];
            foreach ($this->form->emailList as $email) {
                Yii::$app->mailer->compose()
		->setFrom(Yii::$app->params['adminEmail'])
		->setTo($email)
		->setSubject(Yii::$app->params['siteName'].': уведомление')
		->setTextBody(strip_tags($message))
		->send();
                $flag_send=true;
            }
            $this->sendSms();
            return $flag_send;
        }
        else {
            return false;
        }
    }

    private function sendSms() {
        if (AppHelper::getSettingValue('sms.enable') && $this->form->phone) {
            $project=AppHelper::getSettingValue('sms.mainsms.project');
            $key=AppHelper::getSettingValue('sms.mainsms.key');
            $useSSL=AppHelper::getSettingValue('sms.mainsms.useSSL',false);
            $testMode=AppHelper::getSettingValue('sms.mainsms.testMode',true);

            $minHour=str_pad(AppHelper::getSettingValue('sms.minHour','0'), 2,'0',STR_PAD_LEFT);
            $maxHour=str_pad(AppHelper::getSettingValue('sms.maxHour','24'), 2,'0',STR_PAD_LEFT);

            $sms=new SmsMainsms($project, $key, $useSSL, $testMode);
            $message=$this->form->name.'. '.AppHelper::getSettingValue('sms.prefix','');
            foreach ($this->form->fields as $field) {
                $message .= $field->title.': '.$this->{$field->name}.";\n";
            }

            $run_at=null;
            if (date('H')<$minHour) {$run_at=date('d.m.Y').' '.$minHour.':00';}
            elseif (date('H')>$maxHour) {$run_at=date('d.m.Y H:i',  strtotime(date('d.m.Y').' '.$minHour.':00')+24*60*60);}

            //echo '!';
            return $sms->sendSMS($this->form->phone,$message,null,$run_at);
        }
        return false;
    }
}
