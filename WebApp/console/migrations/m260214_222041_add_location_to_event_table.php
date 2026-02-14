<?php

use yii\db\Migration;

class m260214_222041_add_location_to_event_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'location', $this->string(255)->defaultValue(null)->after('description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'location');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260214_222041_add_location_to_event_table cannot be reverted.\n";

        return false;
    }
    */
}
