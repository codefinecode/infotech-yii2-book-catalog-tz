<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Author[] $topAuthors */
/** @var int $year */

$this->title = 'ТОП-10 авторов за ' . $year . ' год';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-top-authors">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <?= Html::beginForm([''], 'get') ?>
            <div class="input-group">
                <?= Html::input('number', 'year', $year, [
                    'class' => 'form-control',
                    'min' => 1000,
                    'max' => date('Y'),
                    'placeholder' => 'Год'
                ]) ?>
                <?= Html::submitButton('Показать', ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if (empty($topAuthors)): ?>
        <div class="alert alert-info">
            Нет данных за выбранный год
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($topAuthors as $index => $author): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><?= $index + 1 ?>. <?= Html::encode($author->full_name) ?></h5>
                        <p class="mb-1">Количество книг: <?= $author->books_count ?></p>
                    </div>
                    <?= Html::a('Профиль автора', ['author/view', 'id' => $author->id], [
                        'class' => 'btn btn-outline-primary btn-sm'
                    ]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>