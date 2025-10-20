<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
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
            throw new \yii\web\NotFoundHttpException('Автор не найден');
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
}