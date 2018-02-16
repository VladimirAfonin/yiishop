<?php

use yii\db\Migration;

/**
 * Handles the creation of table `business_finance_ratings`.
 */
class m180212_154016_create_business_finance_ratings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('business_finance_ratings', [
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
        $this->dropTable('business_finance_ratings');
    }
}
