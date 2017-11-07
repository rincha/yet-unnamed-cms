<?php

namespace app\modules\files\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150121_125862_files_init extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%files_folder}}', [
            'folder_id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . " DEFAULT NULL",
            'name' => Schema::TYPE_STRING . '(255) NOT NULL',
            'pathname' => Schema::TYPE_STRING . '(255) NOT NULL',
            'description' => Schema::TYPE_TEXT . " DEFAULT NULL",
            'type' => Schema::TYPE_STRING . '(64) DEFAULT NULL',
            'special' => Schema::TYPE_STRING . '(64) DEFAULT NULL',
        ], $tableOptions);
        $this->createIndex('files_folder_parent_id_index', '{{%files_folder}}', ['parent_id']);
        $this->createIndex('files_folder_name_unique', '{{%files_folder}}', ['name', 'parent_id'], true);
        $this->createIndex('files_folder_path_unique', '{{%files_folder}}', ['pathname', 'parent_id'], true);
        $this->addForeignKey('files_folder_parent_id_fk', '{{%files_folder}}', ['parent_id'], '{{%files_folder}}', ['folder_id'], 'CASCADE');

        $this->createTable('{{%files_file}}', [
            'file_id' => Schema::TYPE_PK,
            'folder_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'name' => Schema::TYPE_STRING . '(255) NOT NULL',
            'pathname' => Schema::TYPE_STRING . '(255) NOT NULL',
            'ext' => Schema::TYPE_STRING . '(16) NOT NULL',
            'description' => Schema::TYPE_STRING . '(1024) DEFAULT NULL',
            'info' => Schema::TYPE_TEXT . ' NOT NULL',
            'type' => Schema::TYPE_STRING . '(64) DEFAULT NULL',
            'special' => Schema::TYPE_STRING . '(64) DEFAULT NULL',
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
        $this->createIndex('files_file_name_unique', '{{%files_file}}', ['name', 'ext', 'folder_id'], true);
        $this->createIndex('files_file_path_unique', '{{%files_file}}', ['pathname', 'ext', 'folder_id'], true);
        $this->createIndex('files_file_folder_id_index', '{{%files_file}}', ['folder_id'], false);
        $this->addForeignKey('files_file_folder_id_fk', '{{%files_file}}', ['folder_id'], '{{%files_folder}}', ['folder_id'], 'CASCADE');

        return true;
    }

    public function down() {
        echo "m150121_125862_files_init cannot be reverted.\n";
        return false;
    }

}
