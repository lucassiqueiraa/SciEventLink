<?php

use yii\db\Migration;

class m260203_230232_add_session_id_to_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%article}}', 'session_id', $this->integer()->null()->after('status'));

        $this->createIndex(
            '{{%idx-article-session_id}}',
            '{{%article}}',
            'session_id'
        );

        $this->addForeignKey(
            '{{%fk-article-session_id}}',
            '{{%article}}',
            'session_id',
            '{{%session}}',
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
        $this->dropForeignKey(
            '{{%fk-article-session_id}}',
            '{{%article}}'
        );

        $this->dropIndex(
            '{{%idx-article-session_id}}',
            '{{%article}}'
        );

        $this->dropColumn('{{%article}}', 'session_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260203_230232_add_session_id_to_article_table cannot be reverted.\n";

        return false;
    }
    */
}
