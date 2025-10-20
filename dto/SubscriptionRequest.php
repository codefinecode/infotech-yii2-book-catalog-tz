<?php
declare(strict_types=1);

namespace app\dto;

class SubscriptionRequest
{
    public string $phone;
    public int $authorId;

    public function __construct(string $phone, int $authorId)
    {
        $this->phone = $phone;
        $this->authorId = $authorId;
    }
}
