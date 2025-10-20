<?php

namespace app\services;

use Yii;
use app\models\Book;
use app\models\Author;
use yii\web\UploadedFile;

class BookService
{
    public function createBook($title, $year, $isbn, $authorIds, $description = null, $coverImage = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Валидация бизнес-правил
            if (Book::find()->where(['isbn' => $isbn])->exists()) {
                throw new \DomainException('Книга с таким ISBN уже существует');
            }

            foreach ($authorIds as $authorId) {
                if (!Author::find()->where(['id' => $authorId])->exists()) {
                    throw new \DomainException("Автор {$authorId} не найден");
                }
            }

            // Создание книги
            $book = new Book();
            $book->title = $title;
            $book->year = $year;
            $book->isbn = $isbn;
            $book->description = $description;
            $book->authorIds = $authorIds;

            if ($coverImage instanceof UploadedFile) {
                $book->coverImageFile = $coverImage;
            }

            if (!$book->save()) {
                throw new \DomainException('Ошибка сохранения книги: ' . implode(', ', $book->firstErrors));
            }

            $transaction->commit();
            return $book;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function updateBook($book, $title, $year, $isbn, $authorIds, $description = null, $coverImage = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Проверка уникальности ISBN (исключая текущую книгу)
            $exists = Book::find()
                ->where(['isbn' => $isbn])
                ->andWhere(['!=', 'id', $book->id])
                ->exists();

            if ($exists) {
                throw new \DomainException('Книга с таким ISBN уже существует');
            }

            foreach ($authorIds as $authorId) {
                if (!Author::find()->where(['id' => $authorId])->exists()) {
                    throw new \DomainException("Автор {$authorId} не найден");
                }
            }

            // Обновление книги
            $book->title = $title;
            $book->year = $year;
            $book->isbn = $isbn;
            $book->description = $description;
            $book->authorIds = $authorIds;

            if ($coverImage instanceof UploadedFile) {
                $book->coverImageFile = $coverImage;
            }

            if (!$book->save()) {
                throw new \DomainException('Ошибка обновления книги: ' . implode(', ', $book->firstErrors));
            }

            $transaction->commit();
            return $book;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}