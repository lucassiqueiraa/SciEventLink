<?php

use yii\db\Migration;

class m251208_195717_add_created_by_to_event_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%event}}', 'created_by', $this->integer());
        $this->addColumn('{{%event}}', 'updated_by', $this->integer());

        $this->addForeignKey(
            'fk-event-created_by',
            '{{%event}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-event-updated_by',
            '{{%event}}',
            'updated_by',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-event-updated_by', '{{%event}}');
        $this->dropForeignKey('fk-event-created_by', '{{%event}}');

        $this->dropColumn('{{%event}}', 'updated_by');
        $this->dropColumn('{{%event}}', 'created_by');
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
