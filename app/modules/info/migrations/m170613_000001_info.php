<?php
namespace app\modules\info\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170613_000001_info extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%info}}', 'safe', Schema::TYPE_BOOLEAN." DEFAULT '0'");
        return true;
    }

    public function down() {
        echo "m170613_000001_info cannot be reverted.\n";

        return false;
    }
}
