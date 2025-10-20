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
echo Nav::widget([
    'options' => ['class' => 'navbar-nav me-auto'],
    'items' => [
        ['label' => 'Книги', 'url' => ['/book/index']],
        ['label' => 'Авторы', 'url' => ['/author/index']],
        ['label' => 'Отчеты', 'url' => ['/report/top-authors']],
    ],
]);
echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => [
        Yii::$app->user->isGuest ? (
            ['label' => 'Войти', 'url' => ['/site/login']]
        ) : (
            '<li class="nav-item">'
            . Html::beginForm(['/site/logout'])
            . Html::submitButton(
                'Выйти (' . Yii::$app->user->identity->username . ')',
                ['class' => 'nav-link btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'
        )
    ],
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