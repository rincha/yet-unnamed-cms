<?php

namespace app\modules\promo\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

class m151230_201152_promo extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%promo}}', [
            'promo_id' => Schema::TYPE_PK,
            'uid' => Schema::TYPE_STRING . '(64) NULL',
            'name' => Schema::TYPE_STRING. "(255) NOT NULL",
            'meta_title' => Schema::TYPE_STRING. "(255) NULL",
            'meta_description' => Schema::TYPE_STRING. "(1024) NULL",
            'keywords' => Schema::TYPE_STRING. "(1024) NULL",
            'status' => Schema::TYPE_INTEGER. " NOT NULL DEFAULT '0'",
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        $this->createIndex('promo_uid_unique', '{{%promo}}', ['uid'], true);

        $this->createTable('{{%promo_block}}', [
            'block_id' => Schema::TYPE_PK,
            'promo_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING. "(64) NOT NULL",
            'background_color' => Schema::TYPE_STRING. "(16) NULL",
            'background_image' => Schema::TYPE_STRING. "(255) NULL",
            'content' => Schema::TYPE_TEXT. " NULL",
            'script' => Schema::TYPE_TEXT. " NULL",
            'style' => Schema::TYPE_TEXT. " NULL",
            'params' => Schema::TYPE_STRING. "(512) NULL",
            'status' => Schema::TYPE_INTEGER. " NOT NULL DEFAULT '0'",
            'sort_order' => Schema::TYPE_INTEGER. " NOT NULL DEFAULT '0'",
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        $this->createIndex('promo_block_name_unique', '{{%promo_block}}', ['name'], true);


        $this->addForeignKey(
                'promo_block_promo_id_fk', '{{%promo_block}}', 'promo_id', '{{%promo}}', 'promo_id', 'RESTRICT', 'RESTRICT'
        );

        return true;
    }

    public function down() {
        echo "m151230_201152_promo cannot be reverted.\n";
        return false;
    }

}
