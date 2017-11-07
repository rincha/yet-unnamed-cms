<?php

namespace app\modules\user\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

class m151230_182955_user extends Migration {

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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . '(64) NULL',
            'password_hash' => Schema::TYPE_STRING . '(64) NOT NULL',
            'status' => Schema::TYPE_INTEGER . " DEFAULT '0'",
            'auth_key' => Schema::TYPE_STRING . '(64) NULL',
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
                ], $tableOptions);
        $this->createIndex('user_username_unique', '{{%user}}', 'username', true);
        $this->alterColumn(Yii::$app->authManager->assignmentTable, 'user_id', Schema::TYPE_INTEGER);
        $this->addForeignKey(
                'rbac_assignment_user', Yii::$app->authManager->assignmentTable, 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );
        $authManager = $this->getAuthManager();
        $this->insert($authManager->itemTable, ['name' => 'root', 'type' => 1, 'description' => 'superuser role']);

        $this->insert('{{%user}}', ['username' => 'root', 'status' => \app\models\User::STATUS_ACTIVE, 'password_hash' => Yii::$app->security->generatePasswordHash('root1234'), 'created_at' => new \yii\db\Expression('NOW()')]);
        $this->insert($authManager->assignmentTable, ['user_id' => 1, 'item_name' => 'root']);

        $this->insert($authManager->itemTable, [
            'name' => 'u.default.*',
            'type' => \yii\rbac\Item::TYPE_PERMISSION,
            'description' => '',
        ]);

        $this->insert($authManager->itemChildTable, [
            'parent' => 'Authenticated',
            'child' => 'u.default.*',
        ]);

        return true;
    }

    public function down() {
        echo "m151230_182955_user cannot be reverted.\n";
        return false;
    }

}
