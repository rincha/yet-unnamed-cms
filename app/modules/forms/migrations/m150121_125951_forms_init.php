<?php

namespace app\modules\forms\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150121_125951_forms_init extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%form}}', [
            'form_id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'title' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'description' => Schema::TYPE_TEXT . " DEFAULT NULL",
            'type' => Schema::TYPE_STRING . '(64) DEFAULT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'emails'=>Schema::TYPE_STRING.'(512) DEFAULT NULL',
            'phone'=>Schema::TYPE_STRING.'(32) DEFAULT NULL',
            'status'=>Schema::TYPE_BOOLEAN,
        ], $tableOptions);

        $this->createIndex('form_name_unique', '{{%form}}', ['name'], true);

        $this->createTable('{{%form_field}}', [
            'field_id' => Schema::TYPE_PK,
            'form_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'type_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'name' => Schema::TYPE_STRING . '(128) NOT NULL',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'required' => Schema::TYPE_BOOLEAN . " DEFAULT '0'",
            'params' => Schema::TYPE_STRING . '(512) DEFAULT NULL',
            'options' => Schema::TYPE_TEXT . " DEFAULT NULL",
            'tip' => Schema::TYPE_TEXT . " DEFAULT NULL",
            'sort_order' => Schema::TYPE_INTEGER . " DEFAULT '0'",
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
                ], $tableOptions);

        $this->createIndex('form_field_sort_order_index', '{{%form_field}}', ['sort_order'], false);
        $this->addForeignKey('form_field_form_id_fk', '{{%form_field}}', ['form_id'], '{{%form}}', ['form_id'], 'CASCADE');

        return true;
    }

    public function down() {
        echo "m150121_125951_forms_init cannot be reverted.\n";
        return false;
    }

}
