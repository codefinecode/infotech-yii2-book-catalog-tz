<?php
declare(strict_types=1);

namespace app\repositories;

use app\exceptions\NotFoundException;
use app\exceptions\ValidationException;
use app\models\Book;

class BookRepository implements BookRepositoryInterface
{
    public function existsByIsbn(string $isbn, ?int $excludeId = null): bool
    {
        $query = Book::find()->where(['isbn' => $isbn]);
        if ($excludeId !== null) {
            $query->andWhere(['!=', 'id', $excludeId]);
        }
        return $query->exists();
    }

    public function findById(int $id): ?Book
    {
        return Book::find()->where(['id' => $id])->one();
    }

    public function save(Book $book): void
    {
        if (!$book->save()) {
            throw new ValidationException('Ошибка сохранения книги: ' . implode(', ', $book->firstErrors));
        }
    }
}
