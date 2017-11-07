<?php

namespace app\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_181530_session extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%session}}', [
            'id' => Schema::TYPE_STRING . '(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'expire' => Schema::TYPE_INTEGER,
            'data' => Schema::TYPE_BINARY,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
            'ip' => Schema::TYPE_STRING.'(64) NULL',
            'user_agent' => Schema::TYPE_STRING.'(4096) NULL',
            'PRIMARY KEY (id)'
                ], $tableOptions);
        $this->createIndex('session_user_id_index', '{{%session}}', 'user_id', false);
        return true;
    }

    public function down() {
        echo "m151230_181530_session cannot be reverted.\n";
        return false;
    }

}
