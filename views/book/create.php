<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\forms\BookForm $model */
/** @var app\models\Author[] $authors */

$this->title = 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-create">
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

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>