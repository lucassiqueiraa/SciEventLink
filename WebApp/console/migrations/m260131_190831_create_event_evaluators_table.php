<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%event_evaluators}}`.
 */
class m260131_190831_create_event_evaluators_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%event_evaluators}}', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-event_evaluators-event',
            '{{%event_evaluators}}', 'event_id',
            '{{%event}}', 'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-event_evaluators-user',
            '{{%event_evaluators}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-event_evaluators-user', '{{%event_evaluators}}');
        $this->dropForeignKey('fk-event_evaluators-event', '{{%event_evaluators}}');
        $this->dropTable('{{%event_evaluators}}');
    }
}
