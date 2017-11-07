<?php
namespace app\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

class m170613_000001_mail2 extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%mail}}', 'uid', Schema::TYPE_STRING."(64) NULL");
        $this->createIndex('mail_uid_unique', '{{%mail}}', ['mailer', 'uid'], true);
        return true;
    }

    public function down() {
        

        return false;
    }
}
