<?php

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Html;

/** @var \yii\web\View $this */
/** @var string $content */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
NavBar::begin([
    'brandLabel' => 'Каталог книг',
    'brandUrl' => Yii::$app->homeUrl,
    'options' => ['class' => 'navbar-expand-lg navbar-light bg-light'],
]);

// Основное меню
echo Nav::widget([
    'options' => ['class' => 'navbar-nav me-auto'],
    'items' => [
        ['label' => 'Книги', 'url' => ['/book/index']],
        ['label' => 'Авторы', 'url' => ['/author/index']],
        ['label' => 'Отчеты', 'url' => ['/report/top-authors']],
    ],
]);

// Меню пользователя
$userItems = [];
if (Yii::$app->user->isGuest) {
    $userItems[] = ['label' => 'Демо-вход', 'url' => ['/site/demo-login'], 'linkOptions' => ['class' => 'btn btn-outline-success ms-2']];
    $userItems[] = ['label' => 'Войти', 'url' => ['/site/login'], 'linkOptions' => ['class' => 'btn btn-outline-primary ms-2']];
} else {
    $userItems[] = [
        'label' => 'Выйти (' . Yii::$app->user->identity->username . ')',
        'url' => ['/site/logout'],
        'linkOptions' => ['data-method' => 'post', 'class' => 'btn btn-outline-danger ms-2']
    ];
}

echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => $userItems,
]);

NavBar::end();
?>

<div class="container">
    <?= \yii\bootstrap5\Alert::widget() ?>
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>