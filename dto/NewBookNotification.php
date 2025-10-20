<?php
declare(strict_types=1);

namespace app\dto;

class NewBookNotification
{
    public string $phone;
    public string $authorName;
    public string $bookTitle;
    public int $bookYear;

    public function __construct(string $phone, string $authorName, string $bookTitle, int $bookYear)
    {
        $this->phone = $phone;
        $this->authorName = $authorName;
        $this->bookTitle = $bookTitle;
        $this->bookYear = $bookYear;
    }
}
