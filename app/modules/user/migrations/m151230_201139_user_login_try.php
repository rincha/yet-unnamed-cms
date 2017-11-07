<?php
namespace app\modules\user\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201139_user_login_try extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user_login_try}}', [
            'user_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'count' => Schema::TYPE_INTEGER . " DEFAULT '0'",
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        $this->addPrimaryKey('user_login_try_pk', '{{%user_login_try}}', ['user_id']);
        $this->addForeignKey(
                'user_login_try_user_id_fk', '{{%user_login_try}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );
        return true;
    }

    public function down() {
        echo "m151230_201139_user_login_try cannot be reverted.\n";
        return false;
    }
}
