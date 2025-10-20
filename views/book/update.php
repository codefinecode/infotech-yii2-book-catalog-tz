<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\forms\BookForm $model */
/** @var app\models\Book $book */
/** @var app\models\Author[] $authors */

$this->title = 'Редактировать книгу: ' . $book->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $book->title, 'url' => ['view', 'id' => $book->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="book-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="book-form">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        
        <?= $form->field($model, 'year')->textInput(['type' => 'number']) ?>
        
        <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>
        
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        
        <?= $form->field($model, 'authorIds')->listBox(
            \yii\helpers\ArrayHelper::map($authors, 'id', 'full_name'),
            ['multiple' => true, 'size' => 10]
        )->hint('Для выбора нескольких авторов удерживайте Ctrl') ?>
        
        <?= $form->field($model, 'coverImageFile')->fileInput() ?>
        
        <?php if ($book->cover_image): ?>
        <div class="form-group">
            <label>Текущая обложка:</label><br>
            <?= Html::img($book->getCoverImageUrl(), [
                'class' => 'img-thumbnail',
                'style' => 'max-width: 200px;',
                'alt' => $book->title,
            ]) ?>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Отмена', ['view', 'id' => $book->id], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>