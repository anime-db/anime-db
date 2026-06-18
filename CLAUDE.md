# CLAUDE.md

Этот файл содержит указания для Claude Code (claude.ai/code) при работе с кодом в этом репозитории.

## Agent docs (.claude-docs/)

Операционные знания для агента (читать на лету, точка входа — [.claude-docs/index.md](.claude-docs/index.md)):

| Файл                                                 | Когда читать                                                    |
|------------------------------------------------------|-----------------------------------------------------------------|
| [.claude-docs/index.md](.claude-docs/index.md)       | таблица маршрутизации — какой файл под какую задачу             |
| [.claude-docs/gotchas.md](.claude-docs/gotchas.md)   | натолкнулся на странность; правишь самообновление/Composer-цикл |
| [.claude-docs/glossary.md](.claude-docs/glossary.md) | непонятное имя класса/файла/термина (Job, Container, Notify…)   |
| [.claude-docs/context.md](.claude-docs/context.md)   | кто вызывает сервис, модель угроз, статус легаси-решений        |

## Документация-индекс (результаты аудита)

Глубокая документация лежит в [`docs/`](docs/) (формат Obsidian, md-ссылки):

- [docs/AUDIT.md](docs/AUDIT.md) — сводка аудита, оценка здоровья, приоритеты
- [docs/PROJECT.md](docs/PROJECT.md) — структура каталогов и подсистем, как всё связано
- [docs/TECHNICAL.md](docs/TECHNICAL.md) — глубокая механика (жизненный цикл, события, апдейтер)
- [docs/BUGS.md](docs/BUGS.md) — найденные баги и уязвимости с локациями
- [docs/RECOMMENDATIONS.md](docs/RECOMMENDATIONS.md) — улучшение сопровождаемости и безопасности

## Обзор проекта

Anime DB — десктопное приложение на **Symfony 2.7** для управления домашней коллекцией аниме. Запускается как локальный сервер на порту 56780 и использует **SQLite** в качестве базы данных. Код организован как единый Symfony-бандл: `AnimeDb\Bundle\AnimeDbBundle`.

Этот репозиторий — **скелет проекта + ядровой бандл**. Бизнес-логика (каталог, фильтры-источники) подключается как Composer-пакеты `anime-db/*`. Ядро отвечает за инфраструктуру жизненного цикла, а не за прикладную логику.

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

## Гочи и важные предостережения (MUST / MUST NOT)

### MUST NOT
- **Не редактировать вручную** автогенерируемые файлы: `app/bundles.php`,
  `app/config/{vendor_config,routing,parameters}.yml`, `app/DoctrineMigrations/*` —
  их формируют задачи Composer; правки затрутся.
- Не считать изменения в `AnimeDB` и `app/console` из `git status` реальными — это
  смена режима `755 → 644` из-за монтирования WSL/Windows, не правки содержимого.
  Не коммитить их.

### Гочи
- **`composer.lock` намеренно удаляется** в `Composer::reload()` — это не баг, а
  способ форсировать обновление компонентов до последних совместимых версий.
- **Стек EOL:** PHP ≥5.4, Symfony ~2.7, Guzzle 3.9, PHPUnit ^4.8, `minimum-stability: dev`.
  `composer install` на современной системе, скорее всего, не пройдёт без старого PHP.
- **Рассинхрон версий:** ветка `2.x`, но `composer.json: 0.3.29`, `CHANGELOG` на
  `0.3.16`, апдейтер ограничен `<1.0.0` (см. [docs/BUGS.md](docs/BUGS.md) B3).
- **Самообновление опасно и содержит дефекты** — перед правками здесь читай
  [docs/TECHNICAL.md](docs/TECHNICAL.md#самообновление-приложения) и
  [docs/BUGS.md](docs/BUGS.md) (B1 — потеря пути; S1/S2 — нет проверки подписи, HTTP).
- **`secret` генерируется через `rand()`** — криптографически слаб (BUGS S3).
- Windows-вывод консоли перекодируется в `CP866` (`Console\Output\Windows`).

## Известные критичные дефекты (кратко)
Полный список — [docs/BUGS.md](docs/BUGS.md). Топ:
1. 🔴 B1 — `UpdateItself::onAppDownloadedMergeAppSource` теряет разделитель пути → ломает апдейт.
2. 🔴 S1/S2 — самообновление без проверки подписи; монитор по HTTP.
3. 🟠 S3 — слабый `secret`; S4 — `app_dev.php` с `umask(0000)` и открытым профайлером.
