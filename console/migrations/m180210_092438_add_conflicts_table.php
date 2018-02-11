<?php

use yii\db\Migration;

/**
 * Class m180210_092438_add_conflicts_table
 */
class m180210_092438_add_conflicts_table extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable('conflicts_list', [
            'id' => $this->primaryKey(),
            'name' => $this->text(),
            'link_wiki' => $this->text(),
            'wiki_website' => $this->text(),
            'db_website' => $this->text(),
            'google_website' => $this->text(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('conflicts_list');
    }

}
