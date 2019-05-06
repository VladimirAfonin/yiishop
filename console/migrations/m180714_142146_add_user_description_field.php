<?php

use yii\db\Migration;

/**
 * Class m180714_142146_add_user_description_field
 */
class m180714_142146_add_user_description_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%test_user}}', 'desc', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%test_user}}', 'desc');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180714_142146_add_user_description_field cannot be reverted.\n";

        return false;
    }
    */
}
