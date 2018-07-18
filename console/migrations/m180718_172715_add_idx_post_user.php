<?php

use yii\db\Migration;

/**
 * Class m180718_172715_add_idx_post_user
 */
class m180718_172715_add_idx_post_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-post-user_id', '{{%post}}', 'user_id');
        $this->addForeignKey('fk-post-user_id', '{{%post}}', 'user_id', '{{%test_user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180718_172715_add_idx_post_user cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180718_172715_add_idx_post_user cannot be reverted.\n";

        return false;
    }
    */
}
