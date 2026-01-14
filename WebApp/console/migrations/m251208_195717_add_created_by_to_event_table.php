<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%registration}}`.
 */
class m251208_195717_add_created_by_to_event_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%registration}}', 'checkin_at', $this->dateTime()->null()->after('id'));

        $this->addColumn('{{%registration}}', 'checkin_by', $this->integer()->null()->after('checkin_at'));

        $this->addForeignKey(
            'fk-registration-checkin_by',
            '{{%registration}}',
            'checkin_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-registration-checkin_by', '{{%registration}}');

        $this->dropColumn('{{%registration}}', 'checkin_by');
        $this->dropColumn('{{%registration}}', 'checkin_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251208_195717_add_created_by_to_event_table cannot be reverted.\n";

        return false;
    }
    */
}
