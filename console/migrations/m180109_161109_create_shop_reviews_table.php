<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shop_reviews`.
 */
class m180109_161109_create_shop_reviews_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('shop_reviews', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'vote' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'active' => $this->boolean()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-shop_reviews-product_id', 'shop_reviews', 'product_id');
        $this->createIndex('idx-shop_reviews-user_id', 'shop_reviews', 'user_id');

        $this->addForeignKey('fk-shop_reviews-product_id', 'shop_reviews', 'product_id', 'shop_products', 'id');
        $this->addForeignKey('fk-shop_reviews-user_id', 'shop_reviews', 'user_id', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_reviews');
    }
}
