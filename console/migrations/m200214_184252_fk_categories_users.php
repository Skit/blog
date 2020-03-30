<?php

use yii\db\Migration;

/**
 * Class m200214_184252_fk_categories_users
 */
class m200214_184252_fk_categories_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-categories-creator_id', 'categories', 'creator_id');

        $this->addForeignKey(
            'fk-categories-creator_id-users-id',
            'categories',
            'creator_id',
            'users',
            'id',
            'NO ACTION',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-categories-creator_id-users-id', 'categories');
        $this->dropIndex('idx-categories-creator_id', 'categories');
    }
}
