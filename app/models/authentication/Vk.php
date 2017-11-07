<?php

namespace app\models\authentication;

use Yii;
use yii\helpers\Url;
use yii\authclient\clients\VKontakte;

class Vk extends BaseType {

     /**
      * @property \app\models\UserAuthentication $owner
      */

    protected $owner;

    public $protocol=true;
    private $_client;

    public static function allowedMethods(){
        return ['login'];
    }

     public function sendConfirm() {
        $config=$this->owner->typeConfig;
        $url=$config['url.auth'];
        $params=[
            'client_id'=>$config['client_id'],
            'redirect_uri'=> Url::to(['/user/activate', 'uid' => $this->owner->uid, 'type' => 'vk']),
        ];
        $url.='?';
        return ['redirect' => ''];
    }

    public function getClient() {
        if ($this->_client===null) {
            $config=$this->owner->typeConfig;
            $this->_client=new VKontakte([
                'clientId' => $config['client_id'],
                'clientSecret' => $config['client_secret'],
            ]);
        }
        return $this->_client;
    }

}

?>
