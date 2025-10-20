<?php

namespace app\models;

use Yii;

/**
 * @property int $id
 * @property string $full_name
 * @property int $created_at
 * @property int $updated_at
 * 
 * @property Book[] $books
 * @property AuthorSubscription[] $subscriptions
 */
class Author extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%authors}}';
    }

    public function rules()
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'string', 'max' => 255],
            [['full_name'], 'unique'],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
            ],
        ];
    }

    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('{{%book_authors}}', ['author_id' => 'id']);
    }

    public function getSubscriptions()
    {
        return $this->hasMany(AuthorSubscription::class, ['author_id' => 'id']);
    }

    public function getBooksCount()
    {
        return $this->getBooks()->count();
    }
}