<?php

use yii\db\Migration;

/**
 * Handles the creation of table `test_tags`.
 */
class m180703_171812_create_test_tags_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('test_tags', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->createIndex('idx-test-tags-name', 'test_tags','name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('test_tags');
    }
}
