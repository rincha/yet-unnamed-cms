<?php

namespace app\modules\post\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;
use yii\rbac\DbManager;
use yii\base\InvalidConfigException;

class m171009_000001_post extends Migration {

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
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }

            $this->insert($authManager->itemTable, [
                'name'=>'PostAuthor',
                'type'=> \yii\rbac\Item::TYPE_ROLE,
                'description'=>'',
            ]);

            $this->insert($authManager->itemTable, [
                'name'=>'PostAdmin',
                'type'=> \yii\rbac\Item::TYPE_ROLE,
                'description'=>'',
            ]);

            $this->insert($authManager->itemTable, [
                'name'=>'post.author.*',
                'type'=> \yii\rbac\Item::TYPE_PERMISSION,
                'description'=>'',
            ]);

            $this->insert($authManager->itemTable, [
                'name'=>'post.admin.*',
                'type'=> \yii\rbac\Item::TYPE_PERMISSION,
                'description'=>'',
            ]);

            $this->insert($authManager->itemChildTable, [
                'parent'=>'PostAuthor',
                'child'=> 'post.author.*',
            ]);

            $this->insert($authManager->itemChildTable, [
                'parent'=>'PostAdmin',
                'child'=> 'post.author.*',
            ]);

            $this->insert($authManager->itemChildTable, [
                'parent'=>'PostAdmin',
                'child'=> 'post.admin.*',
            ]);

            $this->createTable('{{%post}}', [
                'post_id' => Schema::TYPE_PK,
                'uid' => Schema::TYPE_STRING . "(64) NULL",
                'author_id' => Schema::TYPE_INTEGER . " NULL",
                'title' => Schema::TYPE_STRING . "(255) NOT NULL",
                'h1' => Schema::TYPE_STRING . "(255) NULL",
                'description' => Schema::TYPE_TEXT . " NULL",
                'content' => Schema::TYPE_TEXT . " NOT NULL",
                'keywords' => Schema::TYPE_STRING. "(2048) NULL",
                'status' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT '0'",
                'created_date' => Schema::TYPE_DATE. " NOT NULL",
                'created_time' => Schema::TYPE_TIME. " NOT NULL",
                'updated_at' => Schema::TYPE_DATETIME. " NULL",
            ], $tableOptions);
            $this->createIndex('post_uid_unique', '{{%post}}', ['uid'], true);
            $this->createIndex('post_author_id_index', '{{%post}}', ['author_id'], false);
            $this->createIndex('post_created_date_index', '{{%post}}', ['created_date'], false);
            $this->createIndex('post_created_time_index', '{{%post}}', ['created_time'], false);
            $this->createIndex('post_status_index', '{{%post}}', ['status'], false);

            $this->addForeignKey(
                'post_author_id_fk', '{{%post}}', 'author_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT'
            );

            $this->createTable('{{%post_comment}}', [
                'comment_id' => Schema::TYPE_PK,
                'post_id' => Schema::TYPE_INTEGER . " NOT NULL",
                'author_nickname' => Schema::TYPE_STRING . "(64) NOT NULL",
                'author_email' => Schema::TYPE_STRING . "(255) NULL",
                'author_id' => Schema::TYPE_INTEGER . " NULL",
                'branch_id' => Schema::TYPE_INTEGER . " NULL",
                'parent_id' => Schema::TYPE_INTEGER . " NULL",
                'content' => Schema::TYPE_TEXT . " NOT NULL",
                'status' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT '0'",
                'created_date' => Schema::TYPE_DATE. " NOT NULL",
                'created_time' => Schema::TYPE_TIME. " NOT NULL",
                'updated_at' => Schema::TYPE_DATETIME. " NULL",
            ], $tableOptions);

            $this->createIndex('post_comment_status_index', '{{%post_comment}}', ['status'], false);
            $this->createIndex('post_comment_post_id_index', '{{%post_comment}}', ['post_id'], false);
            $this->addForeignKey(
                'post_comment_post_id_fk', '{{%post_comment}}', 'post_id', '{{%post}}', 'post_id', 'CASCADE', 'CASCADE'
            );

            $this->createIndex('post_comment_author_id_index', '{{%post_comment}}', ['author_id'], false);
            $this->addForeignKey(
                'post_comment_author_id_fk', '{{%post_comment}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->createIndex('post_comment_branch_id_index', '{{%post_comment}}', ['branch_id'], false);
            $this->addForeignKey(
                'post_comment_branch_id_fk', '{{%post_comment}}', 'branch_id', '{{%post_comment}}', 'comment_id', 'CASCADE', 'CASCADE'
            );
            $this->createIndex('post_comment_parent_id_index', '{{%post_comment}}', ['parent_id'], false);
            $this->addForeignKey(
                'post_comment_parent_id_fk', '{{%post_comment}}', 'parent_id', '{{%post_comment}}', 'comment_id', 'CASCADE', 'CASCADE'
            );
            $this->createIndex('post_comment_created_date_index', '{{%post_comment}}', ['created_date'], false);
            $this->createIndex('post_comment_created_time_index', '{{%post_comment}}', ['created_time'], false);


        } catch (\yii\db\Exception $e) {
            $this->down();
            throw new \yii\db\Exception($e);
        }
        return true;
    }

    public function down() {
        if (in_array($this->db->schema->getRawTableName('{{%post_comment}}'), $this->db->schema->tableNames)) {
            $this->delete('{{%post_comment}}');
            $this->dropTable('{{%post_comment}}');
        }
        if (in_array($this->db->schema->getRawTableName('{{%post}}'), $this->db->schema->tableNames)) {
            $this->delete('{{%post}}');
            $this->dropTable('{{%post}}');
        }
        return false;
    }

}
