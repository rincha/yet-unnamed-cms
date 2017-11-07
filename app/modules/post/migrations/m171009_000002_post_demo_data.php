<?php

namespace app\modules\post\migrations;

use Yii;
use yii\db\Migration;
use app\models\User;
use yii\db\Expression;
use yii\rbac\DbManager;
use yii\base\InvalidConfigException;

class m171009_000002_post_demo_data extends Migration {

    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager() {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function up() {
        if (!Yii::$app->getModule('post')->migrationsDemo) {
            return null;
        }
        try {
            $authManager = $this->getAuthManager();

            $this->insert(
                    '{{%user}}',
                    [
                        'username' => 'postUser',
                        'status' => User::STATUS_ACTIVE,
                        'password_hash' => Yii::$app->security->generatePasswordHash('postuserpass'),
                        'created_at' => new Expression('NOW()')
                    ]
            );
            $this->insert(
                    '{{%user}}',
                    [
                        'username' => 'postAuthor',
                        'status' => User::STATUS_ACTIVE,
                        'password_hash' => Yii::$app->security->generatePasswordHash('postauthorpass'),
                        'created_at' => new Expression('NOW()')
                    ]
            );
            $id=$this->db->getLastInsertID();
            $this->insert($authManager->assignmentTable, ['user_id' => $id, 'item_name' => 'PostAuthor']);

            $this->insert(
                    '{{%user}}',
                    [
                        'username' => 'postAdmin',
                        'status' => User::STATUS_ACTIVE,
                        'password_hash' => Yii::$app->security->generatePasswordHash('postadminpass'),
                        'created_at' => new Expression('NOW()')
                    ]
            );
            $id=$this->db->getLastInsertID();
            $this->insert($authManager->assignmentTable, ['user_id' => $id, 'item_name' => 'PostAdmin']);

        } catch (\yii\db\Exception $e) {
            $this->down();
            throw new \yii\db\Exception($e);
        }
        return true;
    }

    public function down() {
        $this->delete('{{%user}}', 'username IN :username', [':username'=>['postAuthor','postAdmin']]);
        return false;
    }

}
