<?php

namespace app\models\authentication;

use Yii;
use yii\helpers\Url;

class Facebook extends BaseType {

     /**
      * @property \app\models\UserAuthentication $owner
      */

    protected $owner;

    public $protocol=true;
    private $_client;

    public static function allowedMethods(){
        return [];
    }

    public function sendConfirm() {return false;}

    public function getClient() {
        if ($this->_client===null) {
            $config=$this->owner->typeConfig;
            $this->_client=new \yii\authclient\clients\Facebook([
                'clientId' => $config['client_id'],
                'clientSecret' => $config['client_secret'],
            ]);
        }
        return $this->_client;
    }

}

?>
