<?php

namespace app\modules\menu\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170718_000001_menu_item_add_class extends Migration {

    public function up() {
        try {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }
            $this->addColumn(\app\modules\menu\models\MenuItem::tableName(), 'css_class', Schema::TYPE_STRING.'(64) NULL');
        } catch (\yii\db\Exception $e) {
            $this->down();
            throw new \yii\db\Exception($e);
        }
        return true;
    }

    public function down() {
        $columns=$this->db->schema->getTableSchema(\app\modules\menu\models\MenuItem::tableName())->columns;
        if (isset($columns['css_class'])) {
            $this->dropColumn(\app\modules\menu\models\MenuItem::tableName(), 'css_class');
        }
        return false;
    }

}
