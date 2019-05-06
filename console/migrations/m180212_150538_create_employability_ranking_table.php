<?php

use yii\db\Migration;

/**
 * Handles the creation of table `employability_ranking`.
 */
class m180212_150538_create_employability_ranking_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('employability_ranking', [
            'id' => $this->primaryKey(),
            'nid' => $this->integer(),
            'name' => $this->text(),
            'rank_display' => $this->integer(),
            'country' => $this->text(),
            'stars' => $this->integer(),
            'region' => $this->text(),
            'year' => $this->text(),
            'score' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('employability_ranking');
    }
}
