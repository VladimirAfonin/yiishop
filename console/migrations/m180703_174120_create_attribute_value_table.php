<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attribute_value`.
 */
class m180703_174120_create_attribute_value_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('attribute_value', [
            'product_id' => $this->integer()->notNull(),
            'attribute_id' => $this->integer()->notNull(),
            'value' => $this->string()->notNull(),
        ]);

        $this->addPrimaryKey('pk-attribute_value-product_id', 'attribute_value','product_id');
        $this->createIndex('idx-attribute_value-product_id', 'attribute_value', 'product_id');
        $this->createIndex('idx-attribute_value-attribute_id', 'attribute_value', 'attribute_id');
        $this->addForeignKey('fk-attribute_value-product_id', 'attribute_value', 'product_id', 'product','id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-attribute_value-attribute_id', 'attribute_value', 'attribute_id', 'attribute', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('attribute_value');
    }
}
