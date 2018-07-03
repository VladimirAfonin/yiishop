<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product_tag`.
 */
class m180703_172132_create_product_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('product_tag', [
            'product_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-product_tag','product_tag', ['product_id', 'tag_id']);
        $this->createIndex('idx-product_tag-product_id', 'product_tag', 'product_id');
        $this->createIndex('idx-product_tag-tag_id', 'product_tag', 'tag_id');
        $this->addForeignKey('fk-product_tag-product_id', 'product_tag', 'product_id', 'product','id','CASCADE','RESTRICT');
        $this->addForeignKey('fk-product_tag-tag_id','product_tag', 'tag_id','test_tags','id','CASCADE','RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('product_tag');
    }
}
