<?php

namespace app\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170101_000001_mail extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%mail}}', [
            'mail_id' => Schema::TYPE_PK,
            'from' => Schema::TYPE_STRING . "(255) NOT NULL",
            'to' => Schema::TYPE_STRING . "(255) NOT NULL",
            'reply_to' => Schema::TYPE_STRING . "(255) NULL",
            'subject' => Schema::TYPE_STRING . "(255) NOT NULL",
            'text_body' => Schema::TYPE_TEXT . " NULL",
            'html_body' => Schema::TYPE_TEXT . ' NULL',
            'data' => Schema::TYPE_TEXT . ' NULL',
            'mailer' => Schema::TYPE_STRING . "(32) NULL",
            'status' => Schema::TYPE_BOOLEAN . " DEFAULT '0'",
            'created_date' => Schema::TYPE_DATE,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
                ], $tableOptions);

        $this->createIndex('mail_from_index', '{{%mail}}', ['from'], false);
        $this->createIndex('mail_to_index', '{{%mail}}', ['to'], false);
        $this->createIndex('mail_reply_to_index', '{{%mail}}', ['reply_to'], false);
        $this->createIndex('mail_created_date_index', '{{%mail}}', ['created_date'], false);
        $this->createIndex('mail_status_index', '{{%mail}}', ['status'], false);
        return true;
    }

    public function down() {
        if (in_array($this->db->schema->getRawTableName('{{%mail}}'), $this->db->schema->tableNames)) {
            $this->delete('{{%mail}}');
            $this->dropTable('{{%mail}}');
        }
        return false;
    }

}
