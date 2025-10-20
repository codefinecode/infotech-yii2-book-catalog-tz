<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_authors}}`.
 */
class m251020_134255_create_book_authors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('{{%book_authors}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'PRIMARY KEY(book_id, author_id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_authors}}');
    }
}
