<?php

use yii\db\Migration;

/**
 * Handles the creation of table `proxy_list`.
 */
class m180129_061857_create_proxy_list_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable('proxy_list', [
            'id' => $this->primaryKey(),
            'address' => $this->text()->notNull(),
            'created_at' => $this->integer(11)->notNull()
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('proxy_list');
    }
}
