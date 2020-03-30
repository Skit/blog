<?php

use yii\db\Migration;

/**
 * Class m200224_204902_profile_tbl
 */
class m200224_204902_profile_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // TODO для теста
        $this->createTable('user_profiles', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'bio' => $this->string(1000),
            'avatar_url' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createIndex('idx-user_profiles-user_id', 'user_profiles', 'user_id');

        $this->addForeignKey(
            'fk-user_profiles-user_id-users-id',
            'user_profiles',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'RESTRICT');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_profiles-user_id-users-id', 'user_profiles');
        $this->dropTable('user_profiles');
    }

}
