<?php

use yii\db\Migration;

class m251020_141516_add_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Books -> Users
        $this->addForeignKey(
            'fk-books-created_by',
            '{{%books}}',
            'created_by',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        
        // BookAuthors -> Books
        $this->addForeignKey(
            'fk-book_authors-book',
            '{{%book_authors}}',
            'book_id',
            '{{%books}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        // BookAuthors -> Authors
        $this->addForeignKey(
            'fk-book_authors-author',
            '{{%book_authors}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        // Subscriptions -> Authors
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
        echo "m251020_141516_add_foreign_keys cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251020_141516_add_foreign_keys cannot be reverted.\n";

        return false;
    }
    */
}
