<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Author $model */
/** @var app\forms\SubscriptionForm $subscriptionForm */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Информация об авторе</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'full_name',
                            [
                                'attribute' => 'books_count',
                                'label' => 'Количество книг',
                                'value' => function($model) {
                                    return $model->getBooks()->count();
                                },
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Подписка на уведомления</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>
                    
                    <?= $form->field($subscriptionForm, 'phone')->textInput([
                        'placeholder' => '+7 (999) 123-45-67'
                    ]) ?>
                    
                    <?= $form->field($subscriptionForm, 'authorId')->hiddenInput()->label(false) ?>
                    
                    <div class="form-group">
                        <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            При появлении новых книг этого автора вы получите SMS уведомление
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h3>Книги автора</h3>
        <?php if ($model->books): ?>
            <div class="list-group">
                <?php foreach ($model->books as $book): ?>
                    <div class="list-group-item">
                        <h5><?= Html::a(Html::encode($book->title), ['book/view', 'id' => $book->id]) ?></h5>
                        <p class="mb-1">Год: <?= $book->year ?></p>
                        <p class="mb-1">ISBN: <?= $book->isbn ?></p>
                        <?php if ($book->description): ?>
                            <p class="mb-1"><?= Html::encode($book->description) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                У этого автора пока нет книг в каталоге
            </div>
        <?php endif; ?>
    </div>
</div>