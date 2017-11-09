<?php

namespace app\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

class m170419_000001_widgets_2 extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%widget}}', 'name', $this->string(255)->null()->after('title'));
        $this->update('{{%widget}}', ['name'=>new \yii\db\Expression('title')]);

        //TODO fix this after issue https://github.com/yiisoft/yii2/issues/12077 closed
        if ($this->db->driverName === 'pgsql') {
            $this->db->createCommand('ALTER TABLE {{%widget}} '
                    . 'ALTER COLUMN name SET NOT NULL, '
                    . 'ALTER COLUMN name DROP DEFAULT'
                    . '')->execute();
        }
        else {
            $this->alterColumn('{{%widget}}', 'name', $this->string(255)->notNull());
        }

        return true;
    }

    public function down() {
        echo "m170419_000001_widgets_2 cannot be reverted.\n";
        return false;
    }

}
