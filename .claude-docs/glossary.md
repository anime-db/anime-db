---
title: Glossary — термины и имена Anime DB
tags: [memory/repo, glossary, anime-db]
updated: 2026-06-18
---

# Глоссарий (легко трактовать неверно)

Только то, где имя обманывает или нужен контекст.

## Собственные сущности проекта

| Термин / имя              | Что это НА САМОМ ДЕЛЕ                                                                                                                                                                    |
|---------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `Job`                     | НЕ Symfony/Doctrine job и не очередь задач. Это класс одной Composer-операции (`src/Composer/Job/`), напр. «добавить бандл в kernel».                                                    |
| `Container`               | НЕ Symfony DI-контейнер. Это очередь `Job`-ов с приоритетами (`src/Composer/Job/Container.php`).                                                                                         |
| `Manipulator`             | Файловый патчер конфигов на месте (`src/Manipulator/`): bundles.php, routing.yml, composer.json и т.д. Никакого отношения к ORM/БД.                                                      |
| `ScriptHandler`           | Статический мост: Composer-хуки → Symfony-операции (`src/Composer/ScriptHandler.php`). Точка входа всего lifecycle.                                                                      |
| `StoreEvents`             | `final`-классы с КОНСТАНТАМИ имён событий (`Event/{Package,Project,UpdateItself}/StoreEvents.php`). Не «хранилище событий».                                                              |
| `Notify` (job)            | Job, который кладёт событие в ОТЛОЖЕННЫЙ диспетчер (на диск). НЕ уведомление пользователю/по почте.                                                                                      |
| deferred events / `.meta` | Сериализованные на диск объекты событий (`app/cache/dev/events/<имя>/`), доставляются позже `animedb:deliver-events`.                                                                    |
| `ProxyMigration`          | Сгенерированная обёртка над миграцией пакета — переносит миграции плагина в `app/DoctrineMigrations/`.                                                                                   |
| `repackMigrations`        | Правит legacy-формат миграций на лету: `getMigrationClass()` → `getMigration()` (`ScriptHandler`).                                                                                       |
| `getVersionCompatible`    | Превращает semver в сравнимую числовую форму для `version_compare`: `3.2.1-RC2 → 3.2.1.6.2` (`src/Composer/Composer.php:179`). Вес суффикса: dev=1,patch=2,alpha=3,beta=4,stable=5,rc=6. |
| `secret`                  | Symfony security/CSRF-секрет (параметр `parameters.yml`), генерится в `post-install` (`SecretKey`).                                                                                      |
| `root_dir`                | Контекстно-зависим! В `Dispatcher`/`Container` = `app/`; в `UpdateItself` = корень проекта. См. [gotchas.md](gotchas.md).                                                                |

## Файлы и имена, которые путаются

| Имя                                              | Что это                                                                                                       |
|--------------------------------------------------|---------------------------------------------------------------------------------------------------------------|
| `AnimeDB` (в корне)                              | Unix bin-скрипт запуска локального сервера (раньше `bin/service`). НЕ путать с бандлом и не с именем проекта. |
| `AnimeDB_Run.vbs` / `AnimeDB_Stop.vbs`           | Windows-скрипты старта/остановки сервера.                                                                     |
| `AnimeDbBundle` / `AnimeDb\Bundle\AnimeDbBundle` | Сам Symfony-бандл (ядро). Главный класс — `src/AnimeDbAnimeDbBundle.php` (пустой).                            |
| `monitor`                                        | Внешняя C++/Qt утилита в system tray (Windows). Скачивается при обновлении (`%anime_db.monitor%`).            |
| `EVENTS_DIR`                                     | Всегда `/cache/dev/events/` — `dev` даже в prod. См. [gotchas.md](gotchas.md).                                |

## Доменные / внешние компоненты (Composer-пакеты `anime-db/*`)

| Термин                     | Что это                                                                               |
|----------------------------|---------------------------------------------------------------------------------------|
| `catalog-bundle`           | Основная прикладная логика каталога аниме (внешний пакет, не в этом репо).            |
| filler / `*-filler-bundle` | Плагины-источники данных для заполнения каталога: `ani-db`, `shikimori`, `world-art`. |
| «ядро» / core              | Этот репозиторий: скелет проекта + инфраструктурный бандл. Бизнес-логики тут нет.     |
