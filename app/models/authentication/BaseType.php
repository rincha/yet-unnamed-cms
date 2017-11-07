<?php

namespace app\models\authentication;

abstract class BaseType {

        /*
         * @property app\models\UserAuthenication $owner
         */
	protected $owner;
        public $protocol=false;

	public function __construct($owner) {
            $this->owner=$owner;
        }

        public static function allowedMethods(){return [];}

	abstract protected function sendConfirm();

        /**
        * Get auth client
        * @return \yii\authclient\OAuth2
        */
        public function getClient(){return false;}

        /**
        * Send message.
        * @param mixed $message - Array['html'=>'HTML version','text'=>'Text version','short'=>'short text version'] or String
        * @param string $type
        * @param Array $params
        * @return boolean send result
        */
        public function sendMessage( $message, $type=null, Array $params=[]){return false;}

        /**
        * Send message for restet password.
        * @param app\models\UserRestore $restore
        * @return app\models\UserRestore|false
        */
        public function sendRestore( \app\models\UserRestore $restore ){return false;}

}

?>
