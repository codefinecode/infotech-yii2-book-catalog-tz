<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\Book;
use app\services\SmsService;
use app\services\SubscriptionService;

class NotifySubscribersJob extends BaseObject implements JobInterface
{
    public $bookId;

    public function execute($queue)
    {
        $book = Book::findOne($this->bookId);
        if (!$book) {
            Yii::error("Book {$this->bookId} not found for notification", 'queue');
            return;
        }

        $smsService = Yii::$container->get(SmsService::class);
        $subscriptionService = Yii::$container->get(SubscriptionService::class);

        // Получаем всех подписчиков авторов этой книги
        $subscriptions = $subscriptionService->getSubscriptionsByBook($this->bookId);

        foreach ($subscriptions as $subscription) {
            try {
                $success = $smsService->sendNewBookNotification(
                    $subscription->phone,
                    $subscription->author->full_name,
                    $book->title,
                    $book->year
                );

                if ($success) {
                    Yii::info("SMS sent to {$subscription->phone} for book {$book->title}", 'sms');
                } else {
                    Yii::warning("Failed to send SMS to {$subscription->phone}", 'sms');
                }

            } catch (\Exception $e) {
                Yii::error("SMS job failed for {$subscription->phone}: " . $e->getMessage(), 'queue');
            }
        }
    }
}