<?php
namespace app\common\smsgates;

/**
 *
 * @author rincha
 */
abstract class Base {
        /*
         * @property app\models\UserAuthenication $owner
         */
	
	abstract public function __construct($config);

	abstract protected function sendSMS($recipients, $message, $sender = null, $run_at = null);
}
