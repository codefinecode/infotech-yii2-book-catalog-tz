<?php
declare(strict_types=1);

namespace app\repositories;

use app\models\Book;

interface BookRepositoryInterface
{
    public function existsByIsbn(string $isbn, ?int $excludeId = null): bool;
    public function findById(int $id): ?Book;
    public function save(Book $book): void; // throws on error
}
