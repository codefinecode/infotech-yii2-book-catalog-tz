<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Book;
use app\models\Author;
use app\forms\BookForm;

class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['manageBooks'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Book::find()->with(['authors']),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $book = $this->findModel($id);

        return $this->render('view', [
            'model' => $book,
        ]);
    }

    public function actionCreate()
    {
        $form = new BookForm();

        if ($form->load(Yii::$app->request->post()) && $form->create()) {
            Yii::$app->session->setFlash('success', 'Книга успешно создана');
            return $this->redirect(['view', 'id' => $form->getBook()->id]);
        }

        $authors = Author::find()->all();

        return $this->render('create', [
            'model' => $form,
            'authors' => $authors,
        ]);
    }

    public function actionUpdate($id)
    {
        $book = $this->findModel($id);
        $form = new BookForm($book);

        if ($form->load(Yii::$app->request->post()) && $form->update()) {
            Yii::$app->session->setFlash('success', 'Книга успешно обновлена');
            return $this->redirect(['view', 'id' => $book->id]);
        }

        $authors = Author::find()->all();

        return $this->render('update', [
            'model' => $form,
            'book' => $book,
            'authors' => $authors,
        ]);
    }

    public function actionDelete($id)
    {
        $book = $this->findModel($id);
        
        if ($book->delete()) {
            Yii::$app->session->setFlash('success', 'Книга успешно удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении книги');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Book::findWithAuthors()->andWhere(['id' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Книга не найдена');
    }
}