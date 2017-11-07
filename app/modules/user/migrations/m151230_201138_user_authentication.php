<?php
namespace app\modules\user\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201138_user_authentication extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user_authentication}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'type' => Schema::TYPE_STRING . '(64) NOT NULL',
            'uid' => Schema::TYPE_STRING . '(128) NOT NULL',
            'verification' => Schema::TYPE_STRING . "(1024) NULL",
            'verification_expire' => Schema::TYPE_DATETIME,
            'status' => Schema::TYPE_INTEGER . " DEFAULT '0'",
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
                ], $tableOptions);

        $this->createIndex('account_type_user_id_unique', '{{%user_authentication}}', ['user_id', 'type'], true);
        $this->createIndex('account_type_uid_unique', '{{%user_authentication}}', ['type', 'uid'], true);

        $this->createIndex('account_user_id', '{{%user_authentication}}', 'user_id');
        $this->createIndex('account_type', '{{%user_authentication}}', 'type');
        $this->createIndex('account_uid', '{{%user_authentication}}', 'uid');

        $this->addForeignKey(
                'account_user_id_fk', '{{%user_authentication}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );
        return true;
    }

    public function down() {
        echo "m151230_201138_user_authentication cannot be reverted.\n";

        return false;
    }
}
