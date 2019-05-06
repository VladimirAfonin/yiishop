<?php

use yii\db\Migration;

/**
 * Class m180527_113032_edit_shop_categories_table
 */
class m180527_113032_edit_shop_categories_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('shop_categories', [
            'id' => 1,
            'name' => '',
            'slug' => 'root',
            'title' => null,
            'description' => null,
            'lft' => 1,
            'rgt' => 2,
            'depth' => 0
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180527_113032_edit_shop_categories_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180527_113032_edit_shop_categories_table cannot be reverted.\n";

        return false;
    }
    */
}
