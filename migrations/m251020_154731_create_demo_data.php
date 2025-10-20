<?php

use yii\db\Migration;

class m251020_154731_create_demo_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Создаем демо-пользователя
        $this->insert('{{%users}}', [
            'username' => 'demo',
            'email' => 'demo@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('demo123'),
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Создаем демо-книги
        $books = [
            [
                'title' => 'Война и мир',
                'year' => 1869,
                'isbn' => '978-5-699-13799-1',
                'description' => 'Роман-эпопея, описывающий русское общество в эпоху войн против Наполеона',
                'created_at' => time(),
                'updated_at' => time(),
                'created_by' => 1,
            ],
            [
                'title' => 'Преступление и наказание',
                'year' => 1866,
                'isbn' => '978-5-699-12345-6',
                'description' => 'Роман о духовном возрождении человека через страдание',
                'created_at' => time(),
                'updated_at' => time(),
                'created_by' => 1,
            ],
            [
                'title' => 'Анна Каренина',
                'year' => 1877,
                'isbn' => '978-5-699-13456-7',
                'description' => 'Трагическая история любви замужней женщины',
                'created_at' => time(),
                'updated_at' => time(),
                'created_by' => 1,
            ],
        ];

        foreach ($books as $book) {
            $this->insert('{{%books}}', $book);
        }

        // Связываем книги с авторами
        $bookAuthors = [
            ['book_id' => 1, 'author_id' => 1], // Война и мир - Толстой
            ['book_id' => 2, 'author_id' => 2], // Преступление и наказание - Достоевский
            ['book_id' => 3, 'author_id' => 1], // Анна Каренина - Толстой
        ];

        foreach ($bookAuthors as $bookAuthor) {
            $this->insert('{{%book_authors}}', $bookAuthor);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%book_authors}}', ['book_id' => [1, 2, 3]]);
        $this->delete('{{%books}}', ['id' => [1, 2, 3]]);
        $this->delete('{{%users}}', ['username' => 'demo']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251020_154731_create_demo_data cannot be reverted.\n";

        return false;
    }
    */
}
