---
title: Внутренняя структура проекта Anime DB
tags: [audit, anime-db, architecture, structure]
updated: 2026-06-18
---

# Anime DB — внутренняя структура и устройство

> Как организован репозиторий ядра и как его части связаны между собой.
> Глубокая механика — в [TECHNICAL.md](TECHNICAL.md). Сводка — в [AUDIT.md](AUDIT.md).

## 1. Роль репозитория в экосистеме

Это **корневой проект (project skeleton)** Symfony, а не библиотека. В терминах
большого проекта Anime DB это «ядро»: оно тянет за собой остальные компоненты как
Composer-зависимости:

```
anime-db/anime-db          ← ЭТОТ репозиторий (скелет + ядровой бандл)
├── anime-db/catalog-bundle        ← каталог аниме (бизнес-логика)
├── anime-db/app-bundle            ← базовая обвязка
├── anime-db/ani-db-filler-bundle      ┐
├── anime-db/shikimori-filler-bundle   ├ плагины-источники данных (fillers)
└── anime-db/world-art-filler-bundle   ┘
```

Ядро не содержит сущностей каталога. Его задача — **инфраструктура**: установить,
обновить, удалить плагины, прописать их в конфигурацию Symfony, прогнать миграции
и обновить само приложение.

## 2. Карта каталогов

```
anime-db/
├── AnimeDB                 # sh-скрипт запуска/остановки сервера (Linux/служба)
├── composer.json           # зависимости + хуки жизненного цикла (ключевой файл!)
├── composer.lock           # удаляется при reload() для форс-обновления
├── README.md               # инструкция пользователя
├── CHANGELOG.md            # история (заморожена на 0.3.16)
│
├── app/                    # ядро Symfony-приложения
│   ├── AppKernel.php        # реестр бандлов; подключает app/bundles.php
│   ├── AppCache.php         # HTTP-кэш-обёртка
│   ├── console              # CLI-входная точка (Windows-вывод декорируется)
│   ├── router.php           # роутер для встроенного сервера php -S
│   ├── autoload.php         # автозагрузчик + intl-стабы
│   ├── bundles.php          # АВТОГЕНЕРИРУЕТСЯ (список плагинов). Не править вручную
│   ├── config/
│   │   ├── config.yml        # базовая конфигурация фреймворка
│   │   ├── config_{dev,prod,test}.yml
│   │   ├── security.yml      # фаервол Symfony (в основном демо-заготовки)
│   │   ├── routing_dev.yml   # профайлер/wdt
│   │   ├── parameters.yml.dist
│   │   ├── parameters.yml     # АВТОГЕНЕРИРУЕТСЯ (incenteev). secret здесь
│   │   ├── vendor_config.yml  # АВТОГЕНЕРИРУЕТСЯ (слитые конфиги плагинов)
│   │   └── routing.yml        # АВТОГЕНЕРИРУЕТСЯ (роуты плагинов)
│   ├── Resources/
│   │   ├── views/base.html.twig   # корневой шаблон
│   │   └── anime.db               # SQLite-БД (gitignore)
│   └── DoctrineMigrations/   # АВТОГЕНЕРИРУЕМЫЕ обёртки миграций плагинов
│
├── web/                    # публичный веб-корень
│   ├── app.php              # боевой фронт-контроллер (prod)
│   ├── app_dev.php          # dev фронт-контроллер (umask 0000 — см. BUGS)
│   └── favicon.ico
│
└── src/                    # бандл AnimeDb\Bundle\AnimeDbBundle (PSR-4)
    ├── AnimeDbAnimeDbBundle.php
    ├── Client/             # HTTP-клиент GitHub API
    ├── Command/            # animedb:update, animedb:deliver-events
    ├── Composer/           # ScriptHandler + Job-система + обёртка Composer
    ├── Console/Output/     # декоратор вывода для Windows (CP866)
    ├── DependencyInjection/
    ├── DoctrineMigrations/ # ProxyMigration (ленивая обёртка миграций)
    ├── Event/              # Dispatcher отложенных событий + события + слушатели
    ├── Manipulator/        # файловые «писатели» конфигов
    ├── Resources/config/   # services.yml, parameters.yml
    └── Tests/              # зеркало src/ (PHPUnit)
```

## 3. Подсистемы ядра

### 3.1. `Composer/` — система задач (Job)

Сердце ядра. Хуки Composer (`post-package-install`, `post-update-cmd` и т. д.)
вызывают статические методы [`ScriptHandler`](TECHNICAL.md#scripthandler), которые
ставят в очередь объекты `Job` через `Composer\Job\Container`. Каждая задача — это
одно действие над одним пакетом:

| Группа задач               | Что делает                                                  |
|----------------------------|-------------------------------------------------------------|
| `Job/Kernel/{Add,Remove}`  | регистрирует/убирает бандл в `app/bundles.php`              |
| `Job/Routing/{Add,Remove}` | подключает `routing.yml` плагина в `app/config/routing.yml` |
| `Job/Config/{Add,Remove}`  | подключает `config.yml` плагина в `vendor_config.yml`       |
| `Job/Migrate/{Up,Down}`    | готовит/откатывает миграции плагина                         |
| `Job/Notify/Package/*`     | шлёт события `installed/updated/removed`                    |
| `Job/Notify/Project/*`     | шлёт события установки/обновления самого приложения         |

Задачи имеют **приоритеты** (`PRIORITY_INSTALL=1`, `PRIORITY_INIT=2`,
`PRIORITY_EXEC=3`) и выполняются по возрастанию: сначала правится ядро/конфиги,
затем готовятся миграции, затем — всё остальное.

### 3.2. `Manipulator/` — файловые писатели

Набор классов, которые **патчат конфигурацию на месте**. Используются и задачами
Composer, и слушателями событий.

```
ManipulatorInterface (маркер)
├── FileContent (абстракция read/write файла)
│   ├── Composer  (composer.json — JSON)
│   ├── Kernel    (bundles.php — PHP-массив)
│   └── Yaml (абстракция YAML)
│       ├── Config      (vendor_config.yml — секция imports)
│       ├── Routing     (routing.yml)
│       └── Parameters  (parameters.yml — get/set ключей, в т.ч. secret)
└── PhpIni (php.ini — парсинг/запись, memory_limit)
```

### 3.3. `Event/` — отложенные события

`Event\Dispatcher` оборачивает Symfony EventDispatcher механизмом доставки через
диск. Во время работы Composer ядро не загружено, поэтому события **сериализуются**
в `app/cache/dev/events/<имя_события>/<md5>.meta` и доставляются позже командой
`animedb:deliver-events`. Подробно — [TECHNICAL.md](TECHNICAL.md#отложенные-события).

Типы событий: `Package` (installed/updated/removed), `Project` (installed/updated),
`UpdateItself` (downloaded/updated).

### 3.4. `Command/` — CLI

| Команда                  | Класс                  | Назначение                                                           |
|--------------------------|------------------------|----------------------------------------------------------------------|
| `animedb:update`         | `UpdateCommand`        | проверить GitHub на новую версию и обновить приложение и зависимости |
| `animedb:deliver-events` | `DeliverEventsCommand` | доставить отложенные события с диска                                 |

> ⚠️ В `AnimeDB`-скрипте также вызывается `animedb:task-scheduler`, но эта команда
> определена не в ядре, а в одном из плагинов (`app-bundle`/`catalog-bundle`).

### 3.5. `Event/Listener/Request/` — HTTP-слушатели

| Слушатель     | Приоритет  | Назначение                                                                    |
|---------------|------------|-------------------------------------------------------------------------------|
| `Firewall`    | -255       | пускает только localhost и приватные сети; режет запросы с прокси-заголовками |
| `StaticFiles` | -256       | раздаёт статику из `web/` напрямую (CSS/JS/媒体), с кэшем в prod                |

## 4. Точки входа и запуск

```
Пользователь
   │
   ├─(Linux)  ./AnimeDB start ──► php -S 0.0.0.0:56780 -t web app/router.php
   │                              └─► app/router.php ──► web/app.php (prod)
   │                              └─ параллельно: app/console animedb:task-scheduler
   │
   └─(Windows) AnimeDB.exe (монитор в трее) ──► тот же встроенный сервер
                                                config.ini задаёт addr/port/php
   Браузер ──► http://localhost:56780/
```

`app/router.php` отдаёт `/update.log` и `/app_dev.php` напрямую, остальное — через
`web/app.php`.

## 5. Что генерируется автоматически (НЕ редактировать руками)

- `app/bundles.php` — список установленных плагинов
- `app/config/vendor_config.yml` — слитые конфиги плагинов
- `app/config/routing.yml` — роуты плагинов
- `app/config/parameters.yml` — параметры (вкл. `secret`)
- `app/DoctrineMigrations/*` — обёртки миграций плагинов

Все они в `.gitignore`. Их формируют задачи Composer и манипуляторы.

## 6. Связь компонентов (поток установки плагина)

```
composer require anime-db/some-bundle
        │
        ▼  Composer выпускает событие post-package-install
ScriptHandler::packageInKernel/Routing/Config/migratePackage/notifyPackage
        │  (каждый ставит Job в Container)
        ▼  ScriptHandler::execJobs  (post-install/update-cmd)
Container сортирует Job по приоритету и выполняет:
   1) Kernel\Add   → bundles.php
   2) Routing\Add  → routing.yml         (через Manipulator)
   3) Config\Add   → vendor_config.yml
   4) Migrate\Up   → обёртки в app/DoctrineMigrations/
   5) Notify       → событие в events/*.meta
        │
        ▼  ScriptHandler::migrateUp → doctrine:migrations:migrate
        ▼  ScriptHandler::deliverEvents → animedb:deliver-events (доставка событий)
```

Детальная механика каждого шага — в [TECHNICAL.md](TECHNICAL.md).
