<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shop_brands`.
 */
class m180109_163509_create_shop_brands_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_brands', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
            'meta_json' => $this->text()->notNull()
        ]);

        $this->createIndex('idx-shop_brands-slug', 'shop_brands','slug', true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_brands');
    }
}
