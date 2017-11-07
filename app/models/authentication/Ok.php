<?php

namespace app\models\authentication;

use Yii;
use yii\helpers\Url;
use app\common\oauth2\Odnoklassniki;

class Ok extends BaseType {

     /**
      * @property \app\models\UserAuthentication $owner
      */

    protected $owner;

    public $protocol=true;
    private $_client;

    public static function allowedMethods(){
        return [];
    }

    public function sendConfirm() {
        return false;
    }

    public function getClient() {
        if ($this->_client===null) {
            $config=$this->owner->typeConfig;
            $this->_client=new Odnoklassniki([
                'clientId' => $config['client_id'],
                'clientSecret' => $config['client_secret'],
                'applicationKey' => $config['application_key'],
            ]);
        }
        return $this->_client;
    }

}

?>
