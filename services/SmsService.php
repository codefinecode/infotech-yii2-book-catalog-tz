<?php
declare(strict_types=1);

namespace app\services;

use Yii;
use GuzzleHttp\Client;
use app\dto\NewBookNotification;

class SmsService
{
    private string $apiKey;
    private string $baseUrl = 'https://smspilot.ru/api.php';

    public function __construct(string $apiKey = 'emulator_key')
    {
        $this->apiKey = $apiKey;
    }

    public function sendFromDto(NewBookNotification $dto): bool
    {
        $message = "Новая книга автора {$dto->authorName}: {$dto->bookTitle} ({$dto->bookYear})";

        if ($this->apiKey === 'emulator_key') {
            Yii::info("SMS to {$dto->phone}: {$message}", 'sms');
            return true;
        }

        try {
            $client = new Client(['timeout' => 10]);
            $response = $client->get($this->baseUrl, [
                'query' => [
                    'send' => $message,
                    'to' => $dto->phone,
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ]
            ]);

            $result = json_decode((string)$response->getBody(), true);
            return isset($result['send'][0]['status']) && $result['send'][0]['status'] === '0';

        } catch (\Throwable $e) {
            Yii::error("SMS sending failed to {$dto->phone}: " . $e->getMessage(), 'sms');
            return false;
        }
    }

    // Backward-compatible wrapper
    public function sendNewBookNotification($phone, $authorName, $bookTitle, $bookYear): bool
    {
        $dto = new NewBookNotification((string)$phone, (string)$authorName, (string)$bookTitle, (int)$bookYear);
        return $this->sendFromDto($dto);
    }
}