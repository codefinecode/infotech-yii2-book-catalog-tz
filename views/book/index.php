<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <?php if (Yii::$app->user->can('manageBooks')): ?>
            <?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            'year',
            'isbn',
            [
                'attribute' => 'authors',
                'value' => function($model) {
                    return implode(', ', \yii\helpers\ArrayHelper::getColumn($model->authors, 'full_name'));
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
                                'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'update' => function($model, $key, $index) {
                        return Yii::$app->user->can('manageBooks');
                    },
                    'delete' => function($model, $key, $index) {
                        return Yii::$app->user->can('manageBooks');
                    },
                ],
            ],
        ],
    ]); ?>
</div>