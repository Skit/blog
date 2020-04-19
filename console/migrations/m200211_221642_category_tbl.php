<?php

use yii\db\Migration;

/**
 * Class m200211_221642_category_tbl
 */
class m200211_221642_category_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('categories', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'content' => $this->text(),
            'meta_data' => $this->json(),
            'creator_id' => $this->integer(11)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->timestamp(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx-categories-status', 'categories', 'status');
        $this->createIndex('idx-categories-creator_id', 'categories', 'creator_id');

        $this->addForeignKey(
            'fk-categories-creator_id-users-id',
            'categories',
            'creator_id',
            'user',
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
        $this->dropForeignKey('fk-categories-creator_id-users-id', 'categories');
        $this->dropTable('categories');
    }
}
