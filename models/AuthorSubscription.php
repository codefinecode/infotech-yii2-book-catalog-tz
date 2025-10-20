<?php

namespace app\models;

use Yii;

class AuthorSubscription extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%author_subscriptions}}';
    }

    public function rules()
    {
        return [
            [['phone', 'author_id'], 'required'],
            [['author_id', 'created_at'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['phone', 'author_id'], 'unique', 'targetAttribute' => ['phone', 'author_id']],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}