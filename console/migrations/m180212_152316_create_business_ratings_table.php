<?php

use yii\db\Migration;

/**
 * Handles the creation of table `business_ratings`.
 */
class m180212_152316_create_business_ratings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('business_ratings', [
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
        $this->dropTable('business_ratings');
    }
}
