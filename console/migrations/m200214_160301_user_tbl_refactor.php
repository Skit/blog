<?php

use yii\db\Migration;

/**
 * Class m200214_160301_user_tbl_refactor
 */
class m200214_160301_user_tbl_refactor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'role',
            $this->string(25)->notNull()->defaultValue('guest')->after('username'));

        $this->alterColumn('user', 'created_at',
            $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));

        $this->alterColumn('user', 'updated_at', $this->timestamp());

        $this->renameTable('user', 'users');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('users', 'user');
        $this->dropColumn('user', 'role');
        $this->alterColumn('user', 'created_at', $this->integer(11)->notNull());
        $this->alterColumn('user', 'updated_at', $this->integer(11)->notNull());
    }

}
