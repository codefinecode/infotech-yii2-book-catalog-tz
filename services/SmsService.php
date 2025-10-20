<?php

namespace app\services;

use Yii;
use GuzzleHttp\Client;

class SmsService
{
    private $apiKey;
    private $baseUrl = 'https://smspilot.ru/api.php';

    public function __construct($apiKey = 'emulator_key')
    {
        $this->apiKey = $apiKey;
    }

    public function sendNewBookNotification($phone, $authorName, $bookTitle, $bookYear)
    {
        $message = "Новая книга автора {$authorName}: {$bookTitle} ({$bookYear})";

        // Эмулятор для тестирования
        if ($this->apiKey === 'emulator_key') {
            Yii::info("SMS to {$phone}: {$message}", 'sms');
            return true;
        }

        try {
            $client = new Client(['timeout' => 10]);
            $response = $client->get($this->baseUrl, [
                'query' => [
                    'send' => $message,
                    'to' => $phone,
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            return isset($result['send'][0]['status']) && $result['send'][0]['status'] === '0';

        } catch (\Exception $e) {
            Yii::error("SMS sending failed to {$phone}: " . $e->getMessage(), 'sms');
            return false;
        }
    }
}