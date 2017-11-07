<?php

namespace app\modules\banner\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170404_000001_banner extends Migration {

    public function up() {
        try {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }

            $this->createTable('{{%banner}}', [
                'banner_id' => Schema::TYPE_PK,
                'type_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'name' => Schema::TYPE_STRING . "(64) NOT NULL",
                'title' => Schema::TYPE_STRING . "(255) NULL",
                'text' => Schema::TYPE_TEXT . " NULL",
                'data' => Schema::TYPE_TEXT . " NULL",
                'start_at' => Schema::TYPE_DATETIME. " NULL",
                'end_at' => Schema::TYPE_DATETIME. " NULL",
                'status' => Schema::TYPE_INTEGER . " NOT NULL",
                'created_at' => Schema::TYPE_DATETIME. " NOT NULL",
                'updated_at' => Schema::TYPE_DATETIME. " NULL",
            ], $tableOptions);

            $this->createTable('{{%banner_item}}', [
                'item_id' => Schema::TYPE_PK,
                'banner_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'sort' => Schema::TYPE_INTEGER . " DEFAULT '0'",
                'image' => Schema::TYPE_STRING . "(255) NULL",
                'link' => Schema::TYPE_STRING . "(255) NULL",
                'title' => Schema::TYPE_STRING . "(255) NULL",
                'text' => Schema::TYPE_TEXT . " NULL",
                'data' => Schema::TYPE_TEXT . " NULL",
                'start_at' => Schema::TYPE_DATETIME. " NULL",
                'end_at' => Schema::TYPE_DATETIME. " NULL",
                'status' => Schema::TYPE_INTEGER . " NOT NULL",
                'created_at' => Schema::TYPE_DATETIME. " NOT NULL",
                'updated_at' => Schema::TYPE_DATETIME. " NULL",
            ], $tableOptions);

            $this->createIndex('banner_item_banner_id_index', '{{%banner_item}}', ['banner_id'], false);
            $this->addForeignKey(
                    'banner_item_banner_id_fk', '{{%banner_item}}', 'banner_id', '{{%banner}}', 'banner_id', 'RESTRICT', 'RESTRICT'
            );


        } catch (\yii\db\Exception $e) {
            $this->down();
            throw new \yii\db\Exception($e);
        }
        return true;
    }

    public function down() {
        if (in_array($this->db->schema->getRawTableName('{{%banner_item}}'), $this->db->schema->tableNames)) {
            $this->delete('{{%banner_item}}');
            $this->dropTable('{{%banner_item}}');
        }
        if (in_array($this->db->schema->getRawTableName('{{%banner}}'), $this->db->schema->tableNames)) {
            $this->delete('{{%banner}}');
            $this->dropTable('{{%banner}}');
        }
        return false;
    }

}
