<?php

namespace app\modules\user\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201140_user_settings extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user_settings}}', [
            'user_id' => Schema::TYPE_INTEGER. " NOT NULL",
            'key' => Schema::TYPE_STRING . "(64) NOT NULL",
            'value' => Schema::TYPE_TEXT . ' NULL',
        ], $tableOptions);
        $this->addPrimaryKey('user_settings_pk', '{{%user_settings}}', ['user_id','key']);
        $this->addForeignKey(
                'user_settings_user_id_fk', '{{%user_settings}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );
        return true;
    }

    public function down() {
        echo "m151230_201140_user_settings cannot be reverted.\n";
        return false;
    }

}
