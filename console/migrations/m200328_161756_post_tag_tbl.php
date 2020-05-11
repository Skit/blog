<?php

use yii\db\Migration;

/**
 * Class m200328_161756_ref_tags_tbl
 */
class m200328_161756_post_tag_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_tag', [
            'tag_id' => $this->integer()->notNull()->unsigned(),
            'post_id' =>  $this->integer()->notNull()->unsigned(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->addPrimaryKey('pk-post_tag', 'post_tag', ['tag_id', 'post_id']);
        $this->createIndex('idx-post_tag-tag_id', 'post_tag', 'tag_id');
        $this->createIndex('idx-post_tag-post_id', 'post_tag', 'post_id');

        $this->addForeignKey(
            'fk-post_tag-tag_id-tags-id',
            'post_tag',
            'tag_id',
            'tags',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-post_tag-post_id-post-id',
            'post_tag',
            'post_id',
            'posts',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $time = $this->beginCommand('CREATE TRIGGER `tag_frequency_up');
        Yii::$app->db->createCommand(
            'CREATE TRIGGER `tag_frequency_up` AFTER INSERT ON `post_tag`
            FOR EACH ROW BEGIN
              UPDATE `tags` SET frequency = frequency + 1;
            END;'
        );
        $this->endCommand($time);

        $time = $this->beginCommand('CREATE TRIGGER `tag_frequency_down`');
        Yii::$app->db->createCommand(
            'CREATE TRIGGER `tag_frequency_down` AFTER INSERT ON `post_tag`
            FOR EACH ROW BEGIN
              UPDATE `tags` SET frequency = frequency - 1;
            END;'
        );
        $this->endCommand($time);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $time = $this->beginCommand('DROP TRIGGER `tag_frequency_up`');
        Yii::$app->db->createCommand('DROP TRIGGER `tag_frequency_up`');
        $this->endCommand($time);
        $time = $this->beginCommand('DROP TRIGGER `tag_frequency_down`');
        Yii::$app->db->createCommand('DROP TRIGGER `tag_frequency_down`');
        $this->endCommand($time);

        $this->dropForeignKey('fk-post_tag-tag_id-tags-id', 'post_tag');
        $this->dropForeignKey('fk-post_tag-post_id-post-id', 'post_tag');
        $this->dropTable('post_tag');
    }
}
