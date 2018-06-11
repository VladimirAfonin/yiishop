<?php

use yii\db\Migration;

/**
 * Class m180112_120818_add_shop_product_table_main_photo_field
 */
class m180112_120818_add_shop_product_table_main_photo_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('shop_products', 'main_photo_id', $this->integer());

        $this->createIndex('idx-shop_products-main_photo_id', 'shop_products', 'main_photo_id');

        $this->addForeignKey('fk-shop_products-main_photo_id', 'shop_products', 'main_photo_id',
            'shop_photos', 'id',
            'SET NULL', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-shop_products-main_photo_id', 'shop_products');
        $this->dropColumn('shop_products', 'main_photo_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180112_120818_add_shop_product_table_main_photo_field cannot be reverted.\n";

        return false;
    }
    */
}
