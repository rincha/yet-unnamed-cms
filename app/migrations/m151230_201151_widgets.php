<?php

namespace app\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

class m151230_201151_widgets extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%widget}}', [
            'widget_id' => Schema::TYPE_PK,
            'type' => Schema::TYPE_STRING.'(64) NOT NULL',
            'title' => Schema::TYPE_STRING . '(255) NULL',
            'content' => Schema::TYPE_TEXT. " NULL",
            'position' => Schema::TYPE_STRING."(32) NOT NULL DEFAULT 'none'",
            'sort_order' => Schema::TYPE_INTEGER." NOT NULL DEFAULT '0'",
            'options' => Schema::TYPE_TEXT. " NULL",
            'allow' => Schema::TYPE_TEXT. " NULL",
            'deny' => Schema::TYPE_TEXT. " NULL",
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        
        $this->createIndex('widget_type_index', '{{%widget}}', ['type'], false);
        $this->createIndex('widget_position_index', '{{%widget}}', ['position'], false);
        $this->createIndex('widget_sort_order_index', '{{%widget}}', ['sort_order'], false);
        
        return true;
    }

    public function down() {
        echo "m151230_201151_widgets cannot be reverted.\n";
        return false;
    }

}
