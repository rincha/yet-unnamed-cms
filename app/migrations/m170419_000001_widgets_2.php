<?php

namespace app\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

class m170419_000001_widgets_2 extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%widget}}', 'name', Schema::TYPE_STRING.'(255) NULL AFTER `type`');
        $this->update('{{%widget}}', ['name'=>new \yii\db\Expression('title')]);
        $this->alterColumn('{{%widget}}', 'name', Schema::TYPE_STRING.'(255) NOT NULL');

        return true;
    }

    public function down() {
        echo "m170419_000001_widgets_2 cannot be reverted.\n";
        return false;
    }

}
