<?php

namespace app\modules\news\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201152_news extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%news}}', [
            'news_id' => Schema::TYPE_PK,
            'type_id'=>Schema::TYPE_INTEGER.' NULL',
            'uid' => Schema::TYPE_STRING . '(64) NULL',
            'name' => Schema::TYPE_STRING. "(255) NOT NULL",
            'h1' => Schema::TYPE_STRING. "(255) NULL",
            'meta_title' => Schema::TYPE_STRING. "(255) NULL",
            'meta_description' => Schema::TYPE_STRING. "(1024) NULL",
            'keywords' => Schema::TYPE_STRING. "(1024) NULL",
            'content' => Schema::TYPE_TEXT. " NULL",
            'images' => Schema::TYPE_TEXT. " NULL",
            'date' => Schema::TYPE_DATETIME,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        $this->createIndex('news_uid_unique', '{{%news}}', ['uid'], true);

        $this->createTable('{{%news_type}}', [
            'type_id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING. "(64) NOT NULL",
            'title' => Schema::TYPE_STRING. "(255) NOT NULL",
        ], $tableOptions);
        $this->createIndex('news_type_name_unique', '{{%news_type}}', ['name'], true);


        $this->addForeignKey(
                'news_type_id_fk', '{{%news}}', 'type_id', '{{%news_type}}', 'type_id', 'SET NULL', 'SET NULL'
        );

        return true;
    }

    public function down() {
        echo "m151230_201152_news cannot be reverted.\n";
        return false;
    }

}
