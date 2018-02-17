<?php

use yii\db\Migration;

/**
 * Handles the creation of table `main_rankings`.
 */
class m180216_150509_create_main_rankings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('main_rankings', [
            'id' => $this->primaryKey(),
            'nid' => $this->integer(),
            'name' => $this->text(),
            'world_ranking' => $this->text(),
            'mba_ranking' => $this->text(),
            'employability_ranking' => $this->text(),
            'business_ratings' => $this->text(),
            'business_finance_ratings' => $this->text(),
            'business_managment_ratings' => $this->text(),
            'year' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('main_rankings');
    }
}
