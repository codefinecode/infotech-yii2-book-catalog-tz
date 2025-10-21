# INFOTECH — Тестовое задание: Каталог книг (Yii2 + MySQL)

Проект реализует каталог книг по условиям тестового задания из вакансии PHP/Yii2 Developer:

- **Книги**: название, год выпуска, описание, ISBN, фото обложки.
- **Авторы**: ФИО, у книги может быть несколько авторов.
- **Доступы**:
  - **Гость**: просмотр, подписка на новые книги автора (по телефону), публичный отчёт.
  - **Юзер**: плюс CRUD по книгам/авторам (через разрешение `manageBooks` в RBAC).
- **Отчёт**: ТОП‑10 авторов, выпустившие больше книг за выбранный год (доступен всем).
- **SMS**: уведомление о новых книгах авторам подписок через SMSPilot (эмулятор).


## Технологический стек

- PHP 8+
- Yii2 (~2.0.45)
- MySQL 8
- Queue: `yii2-queue` (file driver)
- Redis (подключен как зависимость; для задания не обязателен)
- GuzzleHTTP (интеграция с SMSPilot)
- Bootstrap 5 вьюхи (без сложной верстки)
 - Собственные Query Objects (`ActiveQuery`), DTO, Repository Layer, DI, бизнес‑исключения


## Быстрый старт (локально)

1. Установите зависимости
   
   ```bash
   composer install
   ```

2. Настройте БД в `config/db.php`
   
   ```php
   return [
       'class' => 'yii\\db\\Connection',
       'dsn' => 'mysql:host=127.0.0.1;dbname=infotech_books',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8mb4',
   ];
   ```

3. Примените миграции
   
   ```bash
   php yii migrate
   ```

4. Инициализируйте RBAC и назначьте роль
   
   ```bash
   # создаёт разрешение manageBooks и роль user
   php yii rbac/init
   
   # опционально: назначить роль user демо‑пользователю с id=1
   php yii rbac/assign-user-role 1
   ```

5. Создайте демо‑данные (авторы, книги, демо‑пользователь `demo`)
   
   ```bash
   php yii migrate/up m251020_154731_create_demo_data
   ```

6. Запуск веб‑сервера
   
   ```bash
   php yii serve --docroot=web --port=8080
   # http://localhost:8080
   ```


## Уведомления по подпискам (SMSPilot)

- Конфиг в `config/web.php` через DI‑контейнер:
  
  ```php
  'container' => [
      'definitions' => [
          'app\\services\\SmsService' => [
              'apiKey' => 'emulator_key', // для тестов — реальная отправка не производится
          ],
      ],
  ],
  ```

- Новая книга триггерит `jobs/NotifySubscribersJob.php`, который по авторам книги собирает подписчиков и шлёт SMS через `services/SmsService.php`.
- Для file‑очереди запуск воркера не обязателен в рамках проверки кода; задача ставится в очередь. Для полноценной обработки:
  
  ```bash
  php yii queue/listen
  ```

Примечание: в `config/console.php` рекомендуется использовать стандартный контроллер `\yii\queue\command\QueueController`. Если в `controllerMap['queue']` указан кастомный `\app\commands\QueueController`, убедитесь, что класс существует.


## Маршруты

- `GET /books` — список книг (`BookController::actionIndex()`)
- `GET /books/{id}` — просмотр книги
- `GET/POST /books/create` — создание книги (требуется `manageBooks`)
- `GET/POST /books/{id}/edit` — редактирование (требуется `manageBooks`)
- `POST /books/{id}/delete` — удаление (требуется `manageBooks`)
- `GET /authors` — список авторов
- `GET /authors/{id}` — страница автора + форма подписки (гостям)
- `POST /authors/{id}/subscribe` — подписка на автора
- `GET /reports/top-authors[?year=YYYY]` — отчёт ТОП‑10 авторов за год


## Демо‑вход и роли

- В меню есть кнопка «Демо‑вход», также доступен роут `GET /site/demo-login`.
- Роль `user` получает разрешение `manageBooks`. Автор книги — это не пользователь.


## Архитектура и файлы

- Модели: `models/Book.php`, `models/Author.php`, `models/AuthorSubscription.php`, `models/BookAuthor.php`
- Сервисы: `services/BookService.php`, `services/SubscriptionService.php`, `services/SmsService.php`, `services/ReportService.php`
- Формы: `forms/BookForm.php`, `forms/SubscriptionForm.php`
- Очередь и задания: `jobs/NotifySubscribersJob.php`
- Контроллеры: `controllers/BookController.php`, `controllers/AuthorController.php`, `controllers/ReportController.php`
- Вьюхи: `views/book/*`, `views/author/*`, `views/report/top-authors.php`, `views/layouts/main.php`
- RBAC (PhpManager): файлы `rbac/*`, консольный `commands/RbacController.php`
- Миграции: `migrations/*` (создание схемы, внешние ключи, демо‑данные)
- DTO: `dto/BookData.php`, `dto/SubscriptionRequest.php`, `dto/NewBookNotification.php`
- Query Objects: `queries/BookQuery.php`, `queries/AuthorQuery.php`
- Репозитории: `repositories/BookRepositoryInterface.php`, `repositories/BookRepository.php`
- DI: биндинг в `config/web.php` → `BookRepositoryInterface` => `BookRepository`


## Соответствие требованиям теста

- CRUD по книгам/авторам, валидации, связь многие‑ко‑многим.
- Подписка гостя на автора по телефону, имитация SMS через SMSPilot `emulator_key`.
- Отчёт ТОП‑10 авторов за год с кэшированием.
- RBAC: гости видят каталог и отчёт, пользователи с `manageBooks` выполняют CRUD.
- Верстка простая, на Bootstrap 5.
 - Сеньор‑практики: DTO, Query Objects, Repository, DI, бизнес‑исключения, строгие типы в новых классах.


## Что можно докрутить (по времени/желанию)

- Валидация и нормализация телефонных номеров (E.164), маски на форме.
- Хранение обложек на S3/MinIO; генерация превью; валидация MIME.
- Фильтры/поиск по книгам и авторам, пагинация и сортировка расширенные.
- Мидлварь для ограничения частоты подписок с одного номера.
- Перевод RBAC на `DbManager`, миграции ролей.
- Покрытие модульными тестами `services/*` и `forms/*`.
 - Repository для авторов (симметрия), возврат DTO из отчётов, автогенерация превью обложек, маски ввода телефонов.

---

Ниже — оригинальный README шаблона Yii2 для справки по инфраструктуре.

<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Basic Project Template</h1>
    <br>
</p>

Yii 2 Basic Project Template is a skeleton [Yii 2](https://www.yiiframework.com/) application best for
rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![build](https://github.com/yiisoft/yii2-app-basic/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Abuild)

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 7.4.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](https://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](https://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install with Docker

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist
    
Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install    
    
Start the container

    docker-compose up -d
    
You can then access the application through the following URL:

    http://127.0.0.1:8000

**NOTES:** 
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


TESTING
-------

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](https://codeception.com/).
By default, there are 3 test suites:

- `unit`
- `functional`
- `acceptance`

Tests can be executed by running

```
vendor/bin/codecept run
```

The command above will execute unit and functional tests. Unit tests are testing the system components, while functional
tests are for testing user interaction. Acceptance tests are disabled by default as they require additional setup since
they perform testing in real browser. 


### Running  acceptance tests

To execute acceptance tests do the following:  

1. Rename `tests/acceptance.suite.yml.example` to `tests/acceptance.suite.yml` to enable suite configuration

2. Replace `codeception/base` package in `composer.json` with `codeception/codeception` to install full-featured
   version of Codeception

3. Update dependencies with Composer 

    ```
    composer update  
    ```

4. Download [Selenium Server](https://www.seleniumhq.org/download/) and launch it:

    ```
    java -jar ~/selenium-server-standalone-x.xx.x.jar
    ```

    In case of using Selenium Server 3.0 with Firefox browser since v48 or Google Chrome since v53 you must download [GeckoDriver](https://github.com/mozilla/geckodriver/releases) or [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads) and launch Selenium with it:

    ```
    # for Firefox
    java -jar -Dwebdriver.gecko.driver=~/geckodriver ~/selenium-server-standalone-3.xx.x.jar
    
    # for Google Chrome
    java -jar -Dwebdriver.chrome.driver=~/chromedriver ~/selenium-server-standalone-3.xx.x.jar
    ``` 
    
    As an alternative way you can use already configured Docker container with older versions of Selenium and Firefox:
    
    ```
    docker run --net=host selenium/standalone-firefox:2.53.0
    ```

5. (Optional) Create `yii2basic_test` database and update it by applying migrations if you have them.

   ```
   tests/bin/yii migrate
   ```

   The database configuration can be found at `config/test_db.php`.


6. Start web server:

    ```
    tests/bin/yii serve
    ```

7. Now you can run all available tests

   ```
   # run all available tests
   vendor/bin/codecept run

   # run acceptance tests
   vendor/bin/codecept run acceptance

   # run only unit and functional tests
   vendor/bin/codecept run unit,functional
   ```

### Code coverage support

By default, code coverage is disabled in `codeception.yml` configuration file, you should uncomment needed rows to be able
to collect code coverage. You can run your tests and collect coverage with the following command:

```
#collect coverage for all tests
vendor/bin/codecept run --coverage --coverage-html --coverage-xml

#collect coverage only for unit tests
vendor/bin/codecept run unit --coverage --coverage-html --coverage-xml

#collect coverage for unit and functional tests
vendor/bin/codecept run functional,unit --coverage --coverage-html --coverage-xml
```

You can see code coverage output under the `tests/_output` directory.
