<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @modified by rincha
 */

namespace app\migrations;

use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

/**
 * Initializes RBAC tables
 */
class m140506_102106_rbac_init extends \yii\db\Migration {

    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager() {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function up() {
        try {
            $authManager = $this->getAuthManager();
            $this->db = $authManager->db;

            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }

            $this->createTable($authManager->ruleTable, [
                'name' => $this->string(64)->notNull(),
                'data' => $this->text(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY (name)',
                    ], $tableOptions);

            $this->createTable($authManager->itemTable, [
                'name' => $this->string(64)->notNull(),
                'type' => $this->integer()->notNull(),
                'description' => $this->text(),
                'rule_name' => $this->string(64),
                'data' => $this->text(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY (name)',
                'FOREIGN KEY (rule_name) REFERENCES ' . $authManager->ruleTable . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
                    ], $tableOptions);
            $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

            $this->createTable($authManager->itemChildTable, [
                'parent' => $this->string(64)->notNull(),
                'child' => $this->string(64)->notNull(),
                'PRIMARY KEY (parent, child)',
                'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
                'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
                    ], $tableOptions);

            $this->createTable($authManager->assignmentTable, [
                'item_name' => $this->string(64)->notNull(),
                'user_id' => $this->string(64)->notNull(),
                'created_at' => $this->integer(),
                'PRIMARY KEY (item_name, user_id)',
                'FOREIGN KEY (item_name) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
                    ], $tableOptions);

            $paths = ['@app/modules/rbac/rules' => '\app\modules\rbac\rules'];
            $rules = $authManager->getRules();
            foreach ($paths as $path => $namespace) {
                $files = glob(Yii::getAlias($path) . '/*Rule.php');
                foreach ($files as $file) {
                    $className = preg_replace('/^.*\/([a-z0-9_-]*)\.php$/ui', '$1', $file);
                    $className = $namespace . '\\' . $className;
                    $rule = new $className();
                    if (!isset($rules[$rule->name])) {
                        if (!$authManager->add($rule)) {
                            throw new \yii\base\Exception('Error. Rule ' . $rule->name . ' can not be created.');
                        }
                    }
                }
            }

            $this->insert($authManager->itemTable, [
                'name'=>'Authenticated',
                'type'=> \yii\rbac\Item::TYPE_ROLE,
                'description'=>'',
                'rule_name'=>'ItemDataEvalRule',
                'data'=>'return !\Yii::$app->user->isGuest;',
            ]);

        } catch (\yii\db\Exception $e) {
            $this->down();
            throw new \yii\db\Exception($e);
        }
        return true;
    }

    public function down() {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        $tables = [$authManager->assignmentTable, $authManager->itemChildTable, $authManager->itemTable, $authManager->ruleTable];
        foreach ($tables as $table) {
            if (in_array($this->db->schema->getRawTableName($table), $this->db->schema->tableNames)) {
                $this->delete($table);
                $this->dropTable($table);
            }
        }
        return true;
    }

}
