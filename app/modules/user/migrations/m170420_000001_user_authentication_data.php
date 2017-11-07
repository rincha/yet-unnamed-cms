<?php
namespace app\modules\user\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170420_000001_user_authentication_data extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%user_authentication}}', 'data', Schema::TYPE_TEXT.' NULL');
        return true;
    }

    public function down() {
        echo "m170420_000001_user_authentication_data cannot be reverted.\n";

        return false;
    }
}
