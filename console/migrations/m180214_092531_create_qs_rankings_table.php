<?php

use yii\db\Migration;

/**
 * Handles the creation of table `qs_rankings`.
 */
class m180214_092531_create_qs_rankings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('qs_rankings', [
            'id' => $this->primaryKey(),
            'name' => $this->text(),
            'nid' => $this->integer(),
            'data' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('qs_rankings');
    }
}
