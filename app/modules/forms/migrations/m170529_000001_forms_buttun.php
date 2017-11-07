<?php

namespace app\modules\forms\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170529_000001_forms_buttun extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%form}}', 'button', Schema::TYPE_STRING . '(255) DEFAULT NULL');

        return true;
    }

    public function down() {
        echo "m170529_000001_forms_buttun cannot be reverted.\n";
        return false;
    }

}
