<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">
    <h1><?= Html::encode($this->title) ?></h1>

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
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a('Просмотр', $url, ['class' => 'btn btn-sm btn-outline-primary']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>