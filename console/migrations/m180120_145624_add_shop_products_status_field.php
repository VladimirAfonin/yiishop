<?php

use yii\db\Migration;

/**
 * Class m180120_145624_add_shop_products_status_field
 */
class m180120_145624_add_shop_products_status_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('shop_products', 'status', $this->smallInteger()->notNull());

        $this->update('shop_products', ['status' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('shop_products', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180120_145624_add_shop_products_status_field cannot be reverted.\n";

        return false;
    }
    */
}
