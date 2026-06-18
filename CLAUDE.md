# CLAUDE.md

Этот файл содержит указания для Claude Code (claude.ai/code) при работе с кодом в этом репозитории.

## Обзор проекта

Anime DB — десктопное приложение на **Symfony 2.7** для управления домашней коллекцией аниме. Запускается как локальный сервер на порту 56780 и использует **SQLite** в качестве базы данных. Код организован как единый Symfony-бандл: `AnimeDb\Bundle\AnimeDbBundle`.

## Команды

```bash
# Установка зависимостей
php composer.phar install

# Обновление зависимостей (также запускает миграции, очищает кэш и т. д.)
php composer.phar update

# Запуск тестов
./bin/phpunit

# Запуск отдельного файла тестов
./bin/phpunit src/Tests/Command/UpdateCommandTest.php

# Консоль Symfony
php app/console <команда>

# Консольные команды приложения
php app/console animedb:update          # проверить GitHub на новую версию приложения и обновиться
php app/console animedb:deliver-events  # сбросить сериализованные отложенные события с диска

# Очистка кэша
php app/console cache:clear
```

## Архитектура

### Структура бандла
- `src/` — основной код бандла (пространство имён `AnimeDb\Bundle\AnimeDbBundle`, PSR-4)
- `app/` — ядро Symfony-приложения, конфигурация, инициализация DoctrineMigrations
- `web/` — публичный веб-корень
- `src/Tests/` — повторяет структуру `src/`

### Жизненный цикл на основе Composer
Ключевой архитектурный паттерн: **хуки Composer запускают операции Symfony**. `ScriptHandler` обрабатывает события pre/post install/update/package и ставит в очередь объекты `Job` через `Composer\Job\Container`. Задачи покрывают:
- `Kernel/` — регистрация/удаление бандлов в `app/bundles.php`
- `Routing/` — добавление/удаление маршрутов бандла в `app/config/routing.yml`
- `Config/` — слияние конфигов бандлов в `app/config/vendor_config.yml`
- `Migrate/` — запуск миграций Doctrine вверх/вниз
- `Notify/` — отправка событий (установлено, обновлено, удалено) для пакетов и проекта

### Система отложенных событий
`Event\Dispatcher` оборачивает EventDispatcher из Symfony **механизмом отложенной доставки через диск**. Во время запусков Composer (когда ядро не загружено) события сериализуются в `.meta`-файлы в каталоге `app/cache/dev/events/<имя_события>/`. При следующем вызове `animedb:deliver-events` (или после install/update) они десериализуются и отправляются. Это развязывает действия времени Composer от обработки событий во время выполнения.

### Ключевые сервисы (определены в `src/Resources/config/services.yml`)
| Сервис                      | Класс                         | Назначение                              |
|-----------------------------|-------------------------------|-----------------------------------------|
| `anime_db.event_dispatcher` | `Event\Dispatcher`            | хранилище и отправка отложенных событий  |
| `anime_db.composer`         | `Composer\Composer`           | обёртка над Composer API для обновления  |
| `anime_db.client.github`    | `Client\GitHub`               | GitHub API (проверка последнего релиза)  |
| `anime_db.manipulator.*`    | `Manipulator\*`               | файловые мутаторы конфигурации           |
| `anime_db.listener.update`  | `Event\Listener\UpdateItself` | слияние файлов при самообновлении        |

### Манипуляторы (`src/Manipulator/`)
Файловые писатели, которые патчат config/routing/kernel/composer.json на месте. Используются как задачами Composer, так и слушателями событий. Каждый реализует `ManipulatorInterface`.

### Процесс самообновления
`UpdateCommand` → GitHub API → скачивание zip-релиза → запуск события `anime_db.update_itself.downloaded` → слушатель `UpdateItself` сливает `composer.json`, конфиги, bin-скрипты → `composer update` → миграции.

## Конфигурация
- `app/config/parameters.yml` — генерируется из `parameters.yml.dist` через `incenteev/composer-parameter-handler`
- `app/config/vendor_config.yml` — генерируется автоматически; содержит слитые конфиги установленных бандлов. **Не редактировать вручную.**
- `app/bundles.php` — автоматически генерируемый список установленных бандлов пакетов. **Не редактировать вручную.**
- Путь к базе данных SQLite: `app/Resources/<database_path>` (задаётся в параметрах)

## Тестирование
Тесты находятся в `src/Tests/` и повторяют пространство имён `src/`. Бутстрап: `app/bootstrap.php.cache`. Вывод покрытия: `build/coverage-clover.xml`.
