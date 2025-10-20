<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if (Yii::$app->user->can('manageBooks')): ?>
            <?= Html::a('Добавить автора', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'full_name',
            [
                'attribute' => 'books_count',
                'label' => 'Количество книг',
                'value' => function($model) {
                    return $model->getBooks()->count();
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a('Просмотр', $url, ['class' => 'btn btn-sm btn-outline-primary']);
                    },
                    'update' => function($url, $model) {
                        return Html::a('Редактировать', $url, ['class' => 'btn btn-sm btn-outline-secondary']);
                    },
                    'delete' => function($url, $model) {
                        return Html::a('Удалить', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data' => [
                                'confirm' => 'Удалить автора?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'update' => function($model, $key, $index) { return Yii::$app->user->can('manageBooks'); },
                    'delete' => function($model, $key, $index) { return Yii::$app->user->can('manageBooks'); },
                ],
            ],
        ],
    ]); ?>
</div>