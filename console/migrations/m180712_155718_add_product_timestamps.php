<?php

use yii\db\Migration;

/**
 * Class m180712_155718_add_product_timestamps
 */
class m180712_155718_add_product_timestamps extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'created_at', $this->integer()->notNull());
        $this->addColumn('{{%product}}', 'updated_at', $this->integer()->notNull());

        $this->createIndex('idx-product-updated_at', '{{%product}}', 'updated_at' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%product}}', 'created_at');
      $this->dropColumn('{{%product}}', 'updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180712_155718_add_product_timestamps cannot be reverted.\n";

        return false;
    }
    */
}
