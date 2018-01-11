<?php

use yii\db\Migration;

/**
 * Class m180111_112839_insert_categories_root
 */
class m180111_112839_insert_categories_root extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex('idx-shop_categories-slug', 'shop_categories', 'slug', true);

        $this->insert('shop_categories', [
           'id' => 1,
            'name' => '',
            'slug' => 'root',
            'title' => null,
            'description' => null,
            'meta_json' => '{}',
            'lft' => 1,
            'rgt' => 2,
            'depth' => 0,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180111_112839_insert_categories_root cannot be reverted.\n";

//        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180111_112839_insert_categories_root cannot be reverted.\n";

        return false;
    }
    */
}
