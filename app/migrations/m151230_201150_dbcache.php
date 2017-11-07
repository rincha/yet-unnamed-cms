<?php

namespace app\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m151230_201150_dbcache extends Migration {

    public function up() {
        try {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }
            $this->createTable('{{%cache}}', [
                'id' => Schema::TYPE_CHAR . '(128) NOT NULL',
                'expire' => Schema::TYPE_INTEGER,
                'data' => "LONGBLOB",
            ], $tableOptions);
            $this->addPrimaryKey('cache_pk', '{{%cache}}', ['id']);
        } catch (\yii\db\Exception $e) {
            $this->down();
            throw new \yii\db\Exception($e);
        }
        return true;
    }

    public function down() {
        if (in_array($this->db->schema->getRawTableName('{{%cache}}'), $this->db->schema->tableNames)) {
            $this->delete('{{%cache}}');
            $this->dropTable('{{%cache}}');
        }
        return false;
    }

}
