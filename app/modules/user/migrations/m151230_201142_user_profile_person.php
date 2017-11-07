<?php
namespace app\modules\user\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201142_user_profile_person extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user_profile_person}}', [
            'user_id' => Schema::TYPE_INTEGER. " NOT NULL",
            'last_name' => Schema::TYPE_STRING . '(127) NOT NULL',
            'first_name' => Schema::TYPE_STRING . '(127) NOT NULL',
            'middle_name' => Schema::TYPE_STRING . '(127) NULL',
            'birthday' => Schema::TYPE_DATE . ' NULL',
        ], $tableOptions);
        $this->addPrimaryKey('user_profile_person_pk', '{{%user_profile_person}}', ['user_id']);
        $this->addForeignKey(
                'user_profile_person_user_id_fk', '{{%user_profile_person}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );
        return true;
    }

    public function down() {
        echo "m151230_201142_user_profile_person cannot be reverted.\n";
        return false;
    }

}
