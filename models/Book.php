<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string $isbn
 * @property string|null $cover_image
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $created_by
 * 
 * @property Author[] $authors
 * @property User $createdBy
 */
class Book extends \yii\db\ActiveRecord
{
    public $authorIds = [];
    public $coverImageFile;

    public static function tableName()
    {
        return '{{%books}}';
    }

    public function rules()
    {
        return [
            [['title', 'year', 'isbn', 'authorIds'], 'safe', 'on' => 'search'],
            [['year'], 'integer', 'min' => 1000, 'max' => (int)date('Y') + 1],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'unique'],
            [['cover_image'], 'string', 'max' => 500],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['authorIds'], 'validateAuthors'],
            [['coverImageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 5],
        ];
    }

    public function validateAuthors($attribute, $params)
    {
        if (empty($this->$attribute)) {
            $this->addError($attribute, 'Выберите хотя бы одного автора');
        }
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_image' => 'Обложка',
            'authorIds' => 'Авторы',
            'coverImageFile' => 'Файл обложки',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
            ],
            'blameable' => [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => null,
            ],
        ];
    }

    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->via('bookAuthors');
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->coverImageFile) {
            $fileName = Yii::$app->security->generateRandomString() . '.' . $this->coverImageFile->extension;
            $filePath = Yii::getAlias('@webroot/uploads/books/') . $fileName;
            
            if ($this->coverImageFile->saveAs($filePath)) {
                // Delete old cover if exists
                if ($this->cover_image && file_exists(Yii::getAlias('@webroot/uploads/books/') . $this->cover_image)) {
                    unlink(Yii::getAlias('@webroot/uploads/books/') . $this->cover_image);
                }
                $this->cover_image = $fileName;
            }
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Update author relations
        if (!$insert) {
            BookAuthor::deleteAll(['book_id' => $this->id]);
        }
        
        foreach ($this->authorIds as $authorId) {
            $bookAuthor = new BookAuthor([
                'book_id' => $this->id,
                'author_id' => $authorId,
            ]);
            $bookAuthor->save();
        }

        // Trigger event for notifications
        if ($insert) {
            Yii::$app->queue->push(new \app\jobs\NotifySubscribersJob([
                'bookId' => $this->id,
            ]));
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->authorIds = $this->getAuthors()->select('id')->column();
    }

    public function getCoverImageUrl()
    {
        if ($this->cover_image) {
            return Yii::$app->request->baseUrl . '/uploads/books/' . $this->cover_image;
        }
        return null;
    }

    public static function findWithAuthors()
    {
        return self::find()->with(['authors']);
    }

    public function search($params)
    {
        $query = Book::find()->with(['authors']);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Добавляем условия фильтрации
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['year' => $this->year])
            ->andFilterWhere(['like', 'isbn', $this->isbn]);

        return $dataProvider;
    }
}