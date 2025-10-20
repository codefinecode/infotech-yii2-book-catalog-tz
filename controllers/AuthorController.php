<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Author;
use app\forms\SubscriptionForm;

class AuthorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'subscribe'],
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
            'query' => Author::find()->with('books'),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $author = Author::find()
            ->with('books')
            ->andWhere(['id' => $id])
            ->one();

        if (!$author) {
            throw new NotFoundHttpException('Автор не найден');
        }

        $subscriptionForm = new SubscriptionForm();
        $subscriptionForm->authorId = $id;

        if ($subscriptionForm->load(Yii::$app->request->post()) && $subscriptionForm->subscribe()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на уведомления');
            return $this->refresh();
        }

        return $this->render('view', [
            'model' => $author,
            'subscriptionForm' => $subscriptionForm,
        ]);
    }

    public function actionSubscribe()
    {
        $form = new SubscriptionForm();

        if ($form->load(Yii::$app->request->post()) && $form->subscribe()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на уведомления');
            return $this->redirect(['author/view', 'id' => $form->authorId]);
        }

        Yii::$app->session->setFlash('error', 'Ошибка при подписке');
        return $this->redirect(Yii::$app->request->referrer ?: ['author/index']);
    }

    public function actionCreate()
    {
        $model = new Author();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Автор создан');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Автор обновлён');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Автор удалён');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить автора');
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Author::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Автор не найден');
    }
}