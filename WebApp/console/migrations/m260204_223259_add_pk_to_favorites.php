<?php

use yii\db\Migration;

class m260204_223259_add_pk_to_favorites extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');

        if ($this->db->getTableSchema('user_session_favorite', true) !== null) {
            $this->dropTable('user_session_favorite');
        }

        $this->createTable('user_session_favorite', [
            'id' => $this->primaryKey(), // Cria o ID Auto Increment
            'user_id' => $this->integer()->notNull(),
            'session_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-unique-favorite',
            'user_session_favorite',
            ['user_id', 'session_id'],
            true
        );

        $this->addForeignKey(
            'fk-favorites-user',
            'user_session_favorite', 'user_id',
            'user', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-favorites-session',
            'user_session_favorite', 'session_id',
            'session', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->dropTable('user_session_favorite');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}