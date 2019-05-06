<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mba_ranking`.
 */
class m180212_145706_create_mba_ranking_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('mba_ranking', [
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
        $this->dropTable('mba_ranking');
    }
}

