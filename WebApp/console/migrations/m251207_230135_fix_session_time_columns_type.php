<?php

use yii\db\Migration;

class m251207_230135_fix_session_time_columns_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('session', 'start_time', $this->dateTime());
        $this->alterColumn('session', 'end_time', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('session', 'start_time', $this->time());
        $this->alterColumn('session', 'end_time', $this->time());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251207_230135_fix_session_time_columns_type cannot be reverted.\n";

        return false;
    }
    */
}
