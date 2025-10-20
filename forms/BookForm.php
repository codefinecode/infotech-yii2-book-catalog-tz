<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Book;
use app\services\BookService;
use app\dto\BookData;

class BookForm extends Model
{
    public $title;
    public $year;
    public $isbn;
    public $description;
    public $authorIds = [];
    public $coverImageFile;

    private $_book;

    public function __construct(Book $book = null, $config = [])
    {
        if ($book) {
            $this->_book = $book;
            $this->attributes = $book->attributes;
            $this->authorIds = $book->authorIds;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['title', 'year', 'isbn', 'authorIds'], 'required'],
            ['year', 'integer', 'min' => 1000, 'max' => (int)date('Y') + 1],
            ['isbn', 'string', 'max' => 20],
            ['isbn', 'validateIsbn'],
            ['description', 'string'],
            ['authorIds', 'each', 'rule' => ['integer']],
            ['authorIds', 'validateAuthors'],
            ['coverImageFile', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 5],
        ];
    }

    public function validateIsbn($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $query = Book::find()->where(['isbn' => $this->$attribute]);
        if ($this->_book) {
            $query->andWhere(['!=', 'id', $this->_book->id]);
        }

        if ($query->exists()) {
            $this->addError($attribute, 'Книга с таким ISBN уже существует');
        }
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
            'isbn' => 'ISBN',
            'description' => 'Описание',
            'authorIds' => 'Авторы',
            'coverImageFile' => 'Обложка',
        ];
    }

    public function create()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->coverImageFile = UploadedFile::getInstance($this, 'coverImageFile');

        try {
            $bookService = Yii::$container->get(BookService::class);
            $dto = new BookData(
                (string)$this->title,
                (int)$this->year,
                (string)$this->isbn,
                (array)$this->authorIds,
                $this->description !== '' ? (string)$this->description : null,
                $this->coverImageFile instanceof UploadedFile ? $this->coverImageFile : null
            );
            $this->_book = $bookService->createBookFromDto($dto);
            return true;
        } catch (\DomainException $e) {
            $this->addError('*', $e->getMessage());
            return false;
        }
    }

    public function update()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->coverImageFile = UploadedFile::getInstance($this, 'coverImageFile');

        try {
            $bookService = Yii::$container->get(BookService::class);
            $dto = new BookData(
                (string)$this->title,
                (int)$this->year,
                (string)$this->isbn,
                (array)$this->authorIds,
                $this->description !== '' ? (string)$this->description : null,
                $this->coverImageFile instanceof UploadedFile ? $this->coverImageFile : null
            );
            $bookService->updateBookFromDto($this->_book, $dto);
            return true;
        } catch (\DomainException $e) {
            $this->addError('*', $e->getMessage());
            return false;
        }
    }

    public function getBook()
    {
        return $this->_book;
    }
}