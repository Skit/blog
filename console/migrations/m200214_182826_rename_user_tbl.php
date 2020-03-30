<?php

use yii\db\Migration;

/**
 * Class m200214_182826_fk_user_categories
 */
class m200214_182826_rename_user_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('user', 'users');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('users', 'user');
    }
}
