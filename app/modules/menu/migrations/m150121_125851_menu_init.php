<?php

namespace app\modules\menu\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150121_125851_menu_init extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%menu}}', [
            'menu_id' => Schema::TYPE_PK,
            'key' => Schema::TYPE_STRING . "(64) NOT NULL",
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'title' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'description' => Schema::TYPE_TEXT . " DEFAULT NULL",
            'type' => Schema::TYPE_STRING . '(64) DEFAULT NULL',
            'options' => Schema::TYPE_STRING . '(1024) DEFAULT NULL',
                ], $tableOptions);
        $this->createIndex('menu_key_unique', '{{%menu}}', ['key'], true);

        $this->createTable('{{%menu_item}}', [
            'menu_item_id' => Schema::TYPE_PK,
            'menu_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'parent_id' => Schema::TYPE_INTEGER . " DEFAULT NULL",
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'title' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'url' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'controller_id' => Schema::TYPE_STRING . '(127) DEFAULT NULL',
            'action_id' => Schema::TYPE_STRING . '(127) DEFAULT NULL',
            'params' => Schema::TYPE_STRING . '(512) DEFAULT NULL',
            'sort_order' => Schema::TYPE_INTEGER . " DEFAULT '0'",
            'icon' => Schema::TYPE_STRING . '(512) DEFAULT NULL',
            'image' => Schema::TYPE_STRING . '(512) DEFAULT NULL',
                ], $tableOptions);
        $this->createIndex('menu_item_sort_order_index', '{{%menu_item}}', ['sort_order'], false);
        $this->addForeignKey('menu_item_menu_id_fk', '{{%menu_item}}', ['menu_id'], '{{%menu}}', ['menu_id'], 'CASCADE');
        $this->addForeignKey('menu_item_menu_item_id_fk', '{{%menu_item}}', ['parent_id'], '{{%menu_item}}', ['menu_item_id'], 'CASCADE');

        return true;
    }

    public function down() {
        echo "m150121_125851_menu_init cannot be reverted.\n";
        return false;
    }

}
