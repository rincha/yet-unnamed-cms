<?php
namespace app\modules\user\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201141_user_restore extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user_restore}}', [
            'type' => Schema::TYPE_STRING . '(64) NOT NULL',
            'uid' => Schema::TYPE_STRING . '(128) NOT NULL',
            'reset_token' => Schema::TYPE_STRING . "(64) NOT NULL",
            'expire' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        $this->addPrimaryKey('user_restore_pk', '{{%user_restore}}', ['type','uid']);
        $this->addForeignKey(
                'user_restore_uid_fk', '{{%user_restore}}', ['type','uid'], '{{%user_authentication}}', ['type','uid'], 'CASCADE', 'CASCADE'
        );
        return true;
    }

    public function down() {
        echo "m151230_201141_user_restore cannot be reverted.\n";
        return false;
    }

}
