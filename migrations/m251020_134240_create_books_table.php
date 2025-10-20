<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m251020_134240_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'year' => $this->integer()->notNull(),
            'description' => $this->text(),
            'isbn' => $this->string(20)->notNull()->unique(),
            'cover_image' => $this->string(500),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
        ]);

        $this->createIndex('idx-books-year', '{{%books}}', 'year');
        $this->createIndex('idx-books-isbn', '{{%books}}', 'isbn');
        $this->createIndex('idx-books-created_by', '{{%books}}', 'created_by');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
