<?php

use yii\db\Migration;

/**
 * Handles the creation of table `category`.
 */
class m180703_165643_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'parent_id' => $this->integer(),
        ]);

        $this->createIndex('idx-category_parent_id', 'category', 'parent_id');
        $this->addForeignKey('fk-category-parent', 'category','parent_id','category','id','SET NULL', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('category');
    }
}
