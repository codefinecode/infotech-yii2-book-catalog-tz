<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%author_subscriptions}}`.
 */
class m251020_134311_create_author_subscriptions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%author_subscriptions}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(20)->notNull(),
            'author_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-subscriptions-phone-author', '{{%author_subscriptions}}', ['phone', 'author_id'], true);
        $this->createIndex('idx-subscriptions-author', '{{%author_subscriptions}}', 'author_id');
        
        $this->addForeignKey(
            'fk-subscriptions-author',
            '{{%author_subscriptions}}',
            'author_id',
            '{{%authors}}',
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
        $this->dropTable('{{%author_subscriptions}}');
    }
}
