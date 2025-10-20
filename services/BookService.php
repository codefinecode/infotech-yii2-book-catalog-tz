<?php
declare(strict_types=1);

namespace app\services;

use Yii;
use app\models\Book;
use app\models\Author;
use yii\web\UploadedFile;
use app\repositories\BookRepositoryInterface;
use app\exceptions\ValidationException;
use app\exceptions\BusinessLogicException;
use app\dto\BookData;

class BookService
{
    private BookRepositoryInterface $books;

    public function __construct(BookRepositoryInterface $books)
    {
        $this->books = $books;
    }

    public function createBookFromDto(BookData $data): Book
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->books->existsByIsbn($data->isbn)) {
                throw new ValidationException('Книга с таким ISBN уже существует');
            }

            foreach ($data->authorIds as $authorId) {
                if (!Author::find()->where(['id' => (int)$authorId])->exists()) {
                    throw new BusinessLogicException("Автор {$authorId} не найден");
                }
            }

            $book = new Book();
            $book->title = $data->title;
            $book->year = $data->year;
            $book->isbn = $data->isbn;
            $book->description = $data->description;
            $book->authorIds = $data->authorIds;

            if ($data->coverImage instanceof UploadedFile) {
                $book->coverImageFile = $data->coverImage;
            }

            $this->books->save($book);
            $transaction->commit();
            return $book;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function updateBookFromDto(Book $book, BookData $data): Book
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->books->existsByIsbn($data->isbn, (int)$book->id)) {
                throw new ValidationException('Книга с таким ISBN уже существует');
            }

            foreach ($data->authorIds as $authorId) {
                if (!Author::find()->where(['id' => (int)$authorId])->exists()) {
                    throw new BusinessLogicException("Автор {$authorId} не найден");
                }
            }

            $book->title = $data->title;
            $book->year = $data->year;
            $book->isbn = $data->isbn;
            $book->description = $data->description;
            $book->authorIds = $data->authorIds;

            if ($data->coverImage instanceof UploadedFile) {
                $book->coverImageFile = $data->coverImage;
            }

            $this->books->save($book);
            $transaction->commit();
            return $book;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // Backward-compatible wrappers (used by existing forms/controllers)
    public function createBook(string $title, int $year, string $isbn, array $authorIds, ?string $description = null, $coverImage = null): Book
    {
        $data = new BookData($title, $year, $isbn, $authorIds, $description, $coverImage instanceof UploadedFile ? $coverImage : null);
        return $this->createBookFromDto($data);
    }

    public function updateBook(Book $book, string $title, int $year, string $isbn, array $authorIds, ?string $description = null, $coverImage = null): Book
    {
        $data = new BookData($title, $year, $isbn, $authorIds, $description, $coverImage instanceof UploadedFile ? $coverImage : null);
        return $this->updateBookFromDto($book, $data);
    }
}