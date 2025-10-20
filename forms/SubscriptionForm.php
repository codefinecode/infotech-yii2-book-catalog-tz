<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\services\SubscriptionService;

class SubscriptionForm extends Model
{
    public $phone;
    public $authorId;

    public function rules()
    {
        return [
            [['phone', 'authorId'], 'required'],
            ['phone', 'match', 'pattern' => '/^[\d\s\-\+\(\)]+$/', 'message' => 'Неверный формат номера'],
            ['authorId', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Номер телефона',
            'authorId' => 'Автор',
        ];
    }

    public function subscribe()
    {
        if (!$this->validate()) {
            return false;
        }

        try {
            $subscriptionService = Yii::$container->get(SubscriptionService::class);
            $subscriptionService->subscribe($this->phone, $this->authorId);
            return true;
        } catch (\DomainException $e) {
            $this->addError('*', $e->getMessage());
            return false;
        }
    }
}