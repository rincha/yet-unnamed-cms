<?php

namespace app\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150121_125840_settings_init extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%settings}}', [
            'setting_id' => Schema::TYPE_PK,
            'key' => Schema::TYPE_STRING . "(64) NOT NULL",
            'value' => Schema::TYPE_TEXT . ' NOT NULL',
            'serialized' => Schema::TYPE_BOOLEAN . " DEFAULT '0'",
                ], $tableOptions);

        $this->createIndex('settings_key_unique', '{{%settings}}', ['key'], true);
        return true;
    }

    public function down() {
        echo "m150121_125840_settings_init cannot be reverted.\n";
        return false;
    }

}
