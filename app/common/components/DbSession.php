<?php

/**
 * @copyright Copyright (c) 2016 rincha263
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\components;

use Yii;
use yii\db\Expression;
use yii\di\Instance;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\StringHelper;

/**
 * @author rincha263
 */
class DbSession extends \yii\web\DbSession {

    public $maxUserSessionsSee = 50;
    public $guestTimeout=null;

    public function init() {
        $this->readCallback = function($fields) {
            return [
                'user_id' => $fields['user_id'],
                'created_at' => $fields['created_at'],
                'updated_at' => $fields['updated_at'],
                'ip' => $fields['ip'],
                'user_agent' => $fields['user_agent'],
            ];
        };
        $this->writeCallback = function ($session) {
            $res = [
                'created_at' => new Expression('IF(created_at,created_at,NOW())'),
                'updated_at' => new Expression('NOW()'),
                'ip' => Yii::$app->request->userIP,
                'user_agent' => StringHelper::truncate(Yii::$app->request->userAgent, 4090),
            ];
            if (Yii::$app->user->getIdentity(false)) {
                $res['user_id'] = Yii::$app->user->getIdentity(false)->getId();
            }
            return $res;
        };
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    public function destroyUserSessions($user_id) {
        if ($user_id !== null) {
            $res = Yii::$app->db->createCommand()
                    ->delete(
                            $this->sessionTable, 'user_id=:user_id && id!=:id', [':user_id' => $user_id, ':id' => $this->id]
                    )
                    ->execute();
        } else {
            return false;
        }
        return $res;
    }

    public function getUserSessions($user_id) {
        if ($user_id !== null) {
            $query = new Query();
            $res = $query->from($this->sessionTable)->where(['user_id' => $user_id])->orderBy(['updated_at' => SORT_DESC])->limit($this->maxUserSessionsSee)->all();
        } else {
            return false;
        }
        return $res;
    }

    /**
     * Composes storage field set for session writing.
     * @param string $id session id
     * @param string $data session data
     * @return array storage fields
     */
    protected function composeFields($id, $data) {
        $fields= parent::composeFields($id, $data);
        if ($this->guestTimeout && (!isset($fields['user_id']) || !$fields['user_id'])) {
            $fields['expire']=time() + $this->guestTimeout;
        }
        return $fields;
    }

}
