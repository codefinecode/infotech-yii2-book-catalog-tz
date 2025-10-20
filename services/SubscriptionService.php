<?php

namespace app\services;

use Yii;
use app\models\AuthorSubscription;

class SubscriptionService
{
    public function subscribe($phone, $authorId)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) < 10) {
            throw new \DomainException('Неверный формат номера телефона');
        }

        // Проверяем существующую подписку
        $exists = AuthorSubscription::find()
            ->where(['phone' => $phone, 'author_id' => $authorId])
            ->exists();

        if ($exists) {
            throw new \DomainException('Вы уже подписаны на этого автора');
        }

        $subscription = new AuthorSubscription([
            'phone' => $phone,
            'author_id' => $authorId,
        ]);

        if (!$subscription->save()) {
            throw new \DomainException('Ошибка сохранения подписки: ' . implode(', ', $subscription->firstErrors));
        }

        return $subscription;
    }

    public function getSubscriptionsByAuthor($authorId)
    {
        return AuthorSubscription::find()
            ->where(['author_id' => $authorId])
            ->all();
    }

    public function getSubscriptionsByBook($bookId)
    {
        // Получаем всех авторов книги и их подписчиков
        return AuthorSubscription::find()
            ->joinWith('author')
            ->innerJoin('{{%book_authors}}', '{{%book_authors}}.author_id = {{%authors}}.id')
            ->where(['{{%book_authors}}.book_id' => $bookId])
            ->all();
    }
}