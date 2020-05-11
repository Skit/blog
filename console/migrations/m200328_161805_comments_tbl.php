<?php

use yii\db\Migration;

/**
 * Class m200328_161805_comments_tbl
 */
class m200328_161805_comments_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('comments', [
            'id' => $this->primaryKey()->unsigned(),
            'content' => $this->text()->notNull(),
            'post_id' => $this->integer()->unsigned()->notNull(),
            'creator_id' => $this->integer()->notNull()->notNull(),
            'parent_id' => $this->integer()->unsigned(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->timestamp(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx-comments-post_id', 'comments', 'post_id');
        $this->createIndex('idx-comments-status', 'comments', 'status');
        $this->createIndex('idx-comments-creator_id', 'comments', 'creator_id');

        $this->addForeignKey(
            'fk-comments-post_id-post-id',
            'comments',
            'post_id',
            'posts',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-comments-creator_id-users-id',
            'comments',
            'creator_id',
            'users',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-comments-post_id-post-id', 'comments');
        $this->dropForeignKey('fk-comments-creator_id-users-id', 'comments');
        $this->dropTable('comments');
    }
}
