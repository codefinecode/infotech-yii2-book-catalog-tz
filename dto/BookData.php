<?php
declare(strict_types=1);

namespace app\dto;

use yii\web\UploadedFile;

class BookData
{
    public string $title;
    public int $year;
    public string $isbn;
    /** @var int[] */
    public array $authorIds;
    public ?string $description;
    public ?UploadedFile $coverImage;

    /**
     * @param int[] $authorIds
     */
    public function __construct(string $title, int $year, string $isbn, array $authorIds, ?string $description = null, ?UploadedFile $coverImage = null)
    {
        $this->title = $title;
        $this->year = $year;
        $this->isbn = $isbn;
        $this->authorIds = array_map('intval', $authorIds);
        $this->description = $description;
        $this->coverImage = $coverImage;
    }
}
