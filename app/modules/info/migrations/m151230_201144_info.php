<?php

namespace app\modules\info\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201144_info extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%info}}', [
            'info_id' => Schema::TYPE_PK,
            'type_id'=>Schema::TYPE_INTEGER.' NULL',
            'uid' => Schema::TYPE_STRING . '(64) NULL',
            'name' => Schema::TYPE_STRING. "(255) NOT NULL",
            'h1' => Schema::TYPE_STRING. "(255) NULL",
            'meta_title' => Schema::TYPE_STRING. "(255) NULL",
            'meta_description' => Schema::TYPE_STRING. "(1024) NULL",
            'keywords' => Schema::TYPE_STRING. "(1024) NULL",
            'content' => Schema::TYPE_TEXT. " NULL",
            'images' => Schema::TYPE_TEXT. " NULL",
            'params' => Schema::TYPE_TEXT,
            'date' => Schema::TYPE_DATETIME,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        $this->createIndex('info_uid_unique', '{{%info}}', ['uid'], true);

        $this->createTable('{{%info_type}}', [
            'type_id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING. "(64) NOT NULL",
            'title' => Schema::TYPE_STRING. "(255) NOT NULL",
        ], $tableOptions);
        $this->createIndex('info_type_name_unique', '{{%info_type}}', ['name'], true);

        $this->createTable('{{%info_relation_type}}', [
            'type_id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING. "(64) NOT NULL",
            'title' => Schema::TYPE_STRING. "(255) NOT NULL",
        ], $tableOptions);
        $this->createIndex('info_relation_type_name_unique', '{{%info_relation_type}}', ['name'], true);

        $this->createTable('{{%info_relation}}', [
            'relation_id' => Schema::TYPE_PK,
            'master_id' => Schema::TYPE_INTEGER,
            'slave_id' => Schema::TYPE_INTEGER,
            'type_id' => Schema::TYPE_INTEGER,
            'sort_order' => Schema::TYPE_INTEGER,
        ], $tableOptions);
        $this->createIndex('info_relation_type_id_index', '{{%info_relation}}', ['type_id'], false);
        $this->createIndex('info_relation_master_id_index', '{{%info_relation}}', ['master_id'], false);
        $this->createIndex('info_relation_slave_id_index', '{{%info_relation}}', ['slave_id'], false);
        $this->createIndex('info_relation_sort_order_index', '{{%info_relation}}', ['sort_order'], false);
        $this->createIndex('info_relation_mst_unique', '{{%info_relation}}', ['master_id','slave_id','type_id'], true);


        $this->addForeignKey(
                'info_type_id_fk', '{{%info}}', 'type_id', '{{%info_type}}', 'type_id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
                'info_relation_type_id_fk', '{{%info_relation}}', 'type_id', '{{%info_relation_type}}', 'type_id', 'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
                'info_relation_master_id_fk', '{{%info_relation}}', 'master_id', '{{%info}}', 'info_id', 'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
                'info_relation_slave_id_fk', '{{%info_relation}}', 'slave_id', '{{%info}}', 'info_id', 'CASCADE', 'CASCADE'
        );
        return true;
    }

    public function down() {
        echo "m151230_201144_info cannot be reverted.\n";
        return false;
    }

}
