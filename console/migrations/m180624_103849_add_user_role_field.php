<?php

use yii\db\Migration;

/**
 * Class m180624_103849_add_user_role_field
 */
class m180624_103849_add_user_role_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user', 'role', $this->string(64));
        $this->update('user', ['role' => 'user']);
        $this->createIndex('idx_user_role', 'user', 'role');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180624_103849_add_user_role_field cannot be reverted.\n";

        return false;
    }
    */
}
