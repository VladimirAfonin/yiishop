<?php

use yii\db\Migration;

/**
 * Class m180112_183037_add_shop_products_description_field
 */
class m180112_183037_add_shop_products_description_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('shop_products', 'description', $this->text()->after('name'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('shop_products', 'description');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180112_183037_add_shop_products_description_field cannot be reverted.\n";

        return false;
    }
    */
}
