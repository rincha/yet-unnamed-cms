<?php

namespace app\modules\user\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;
use app\models\User;

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
        try {
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

        if (Yii::$app instanceof \yii\console\Application && Yii::$app->controller) {
            $user=new \app\models\User();
            $password=Yii::$app->controller->prompt('Enter password for admin user:',[
                'required' => true,
                'validator' => function($input, &$error)use($user) {
                    $user->new_password=$input;
                    if (!$user->validate(['new_password'])) {
                        $error = implode("\n",$user->getErrors('new_password'));
                        return false;
                    }
                    return true;
                }]
            );
        }
        else {
            $password='root1234';
        }

        $this->insert('{{%user}}', [
            'username' => 'admin',
            'status' => \app\models\User::STATUS_ACTIVE,
            'password_hash' => Yii::$app->security->generatePasswordHash($password),
            'created_at' => new \yii\db\Expression('NOW()')
        ]);
        $this->insert($authManager->assignmentTable, ['user_id' => $this->db->lastInsertID, 'item_name' => 'root']);

        $this->insert($authManager->itemTable, [
            'name' => 'u.default.*',
            'type' => \yii\rbac\Item::TYPE_PERMISSION,
            'description' => '',
        ]);

        $this->insert($authManager->itemChildTable, [
            'parent' => 'Authenticated',
            'child' => 'u.default.*',
        ]);
        } catch (\yii\db\Exception $e) {
            $this->down();
            throw new \yii\db\Exception($e);
        }
        return true;
    }

    public function down() {
        if (in_array($this->db->schema->getRawTableName(User::tableName()), $this->db->schema->tableNames)) {
            $authManager = $this->getAuthManager();
            $this->delete($authManager->itemChildTable,['child' => 'u.default.*',]);
            $this->delete($authManager->itemTable,['name' => 'u.default.*']);
            $this->delete($authManager->assignmentTable,['item_name' => 'root']);
            $this->delete(User::tableName(),['username' => 'admin']);
            $this->delete($authManager->itemTable,['name' => 'root']);
            $this->delete(User::tableName());
            if ($this->db->schema->getSchemaForeignKeys(Yii::$app->authManager->assignmentTable)) {
                $this->dropForeignKey('rbac_assignment_user', Yii::$app->authManager->assignmentTable);
            }
            $this->dropTable(User::tableName());
        }
        return true;
    }

}
