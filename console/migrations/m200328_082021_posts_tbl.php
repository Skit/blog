<?php

use yii\db\Migration;

/**
 * Class m200328_082021_posts_tbl
 */
class m200328_082021_posts_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('posts', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'preview' => $this->string(500),
            'content' => $this->text()->notNull(),
            'meta_data' => $this->json(),
            'post_banners' => $this->json(),
            'category_id' => $this->integer(11)->notNull()->unsigned(),
            'creator_id' => $this->integer(11)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'published_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'is_highlight' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'count_view' => $this->integer(11)->unsigned()->defaultValue(0),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx-posts-status', 'posts', 'status');
        $this->createIndex('idx-posts-creator_id', 'posts', 'creator_id');
        $this->createIndex('idx-post-category_id', 'posts', 'category_id');
        $this->createIndex('idx-post-published_at', 'posts', 'published_at');

        $this->addForeignKey(
            'fk-post-category_id-category-id',
            'posts',
            'category_id',
            'categories',
            'id',
            'RESTRICT',
            'RESTRICT'
            );

        $this->addForeignKey(
            'fk-posts-creator_id-users-id',
            'posts',
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
        $this->dropForeignKey('fk-posts-creator_id-users-id', 'posts');
        $this->dropForeignKey('fk-post-category_id-category-id', 'posts');
        $this->dropTable('posts');
    }
}
