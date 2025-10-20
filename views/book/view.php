<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <div>
            <?php if (Yii::$app->user->can('manageBooks')): ?>
                <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'title',
                    'year',
                    'isbn',
                    [
                        'attribute' => 'description',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' => 'authors',
                        'value' => function($model) {
                            return implode(', ', \yii\helpers\ArrayHelper::getColumn($model->authors, 'full_name'));
                        },
                    ],
                    [
                        'attribute' => 'createdBy.username',
                        'label' => 'Добавил',
                    ],
                ],
            ]) ?>
        </div>
        
        <?php if ($model->cover_image): ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Обложка</h5>
                    <?= Html::img($model->getCoverImageUrl(), [
                        'class' => 'img-fluid',
                        'alt' => $model->title,
                    ]) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>