---
title: Gotchas — нетривиальные ловушки Anime DB
tags: [memory/repo, gotcha, anime-db]
updated: 2026-06-18
---

# Гочи (нетривиальные ловушки)

Только то, что **не** понятно за 5 секунд из имени файла/класса. Каждая запись — с
локацией `файл:строка`. Баги (`B1`/`S1`…) детально — в [../docs/BUGS.md](../docs/BUGS.md).

## Composer-цикл и манипуляторы

- **`composer.lock` намеренно удаляется** — `Composer::reload()`
  (`src/Composer/Composer.php:104-108`) делает `unlink($lock_file)`. Выглядит как баг,
  но это стратегия «всегда тянуть последние совместимые версии компонентов». Не
  «чинить».
- **Манипуляторы бросают `RuntimeException` в конструкторе, если файла нет** —
  `src/Manipulator/FileContent.php:24-25` (и `PhpIni.php`). Поэтому
  `ScriptHandler::installConfig` в `pre-install`/`pre-update` ОБЯЗАН заранее создать
  пустые `app/config/vendor_config.yml`, `routing.yml`, `app/bundles.php`. Это не
  «инициализация на всякий случай» — без неё последующие job-ы падают. Менять порядок
  хуков опасно.
- **`Kernel`-манипулятор парсит `app/bundles.php` как ТЕКСТ**, а не как PHP
  (`src/Manipulator/Kernel.php`): ищет `[`…`]`, режет по `\n`, дописывает
  `new \FQCN()`. Хрупко к форматированию файла. Плюс читает `AppKernel.php`, чтобы не
  продублировать уже зашитые там бандлы.
- **`Routing\Add` исключает `sensio/framework-extra-bundle`**
  (`src/Composer/Job/Routing/Add.php:24`) — у него `routing.xml` это список сервисов,
  а не маршруты. Не считать это забытым кейсом.
- **Job-ы исполняются по приоритету, не по порядку добавления** —
  `Container::execute()` (`src/Composer/Job/Container.php:122`) делает `ksort` по
  приоритету: INSTALL(1) → INIT(2) → EXEC(3). Сначала правка kernel/routing/config,
  потом миграции, потом нотификации. `register()` job-а вызывается СРАЗУ при
  `addJob` (`:111`), а `execute()` — позже.
- **`Migrate\Down` почти пустой** — реальный откат делает не job, а
  `ScriptHandler::migrateDown` (`src/Composer/ScriptHandler.php:312`), читая миграции
  из `app/cache/dev/DoctrineMigrations/`. Туда их копирует `register()` job-а ДО
  удаления пакета (иначе после удаления исходники миграций недоступны).
- **`post-update` гоняет `migrateDown` ПЕРЕД `migrateUp`** (composer.json) — сначала
  откат всех версий до 0, затем накат. Намеренно, не дубль.

## Самообновление

- **B1 — merge app source уплощает структуру каталогов.**
  `UpdateItself::onAppDownloadedMergeAppSource` (`src/Event/Listener/UpdateItself.php:136`)
  копирует файлы из `app/DoctrineMigrations/` и `app/Resources/` через
  `$event->getPath().$file->getFilename()` — только ИМЯ файла, без относительного
  пути. Сравни со строкой `:126`, где для `bootstrap.php.cache` берётся полный путь.
  Итог: вложенные каталоги схлопываются в один, коллизии имён, потеря БД/ресурсов в
  подпапках. Это баг, а не «так задумано».
- **Два разных диспетчера в одном flow самообновления.** Событие `DOWNLOADED`
  шлётся через НАСТОЯЩИЙ `event_dispatcher` (синхронно — листенер готовит скачанную
  копию до перезаписи файлов). Событие `UPDATED` — через ОТЛОЖЕННЫЙ
  `anime_db.event_dispatcher` (пишется на диск, доставится после `composer update`).
  См. `src/Command/UpdateCommand.php:95,105`.
- **Апдейтер никогда не предложит версию ≥ 1.0.0** — `GitHub::getLastRelease`
  (`src/Client/GitHub.php:57`) жёстко фильтрует `version_compare($v, '1.0.0', '<')`
  с комментарием «v1.0.0 is BC». Поэтому проект застрял на ветке 0.x (см. B3). Это
  осознанный BC-guard, но фактически — тупик.
- **Самообновление без проверки подписи/чексуммы** (S1) и **скачивание monitor по
  HTTP** (S2). Перед правками апдейтера читай
  [../docs/BUGS.md](../docs/BUGS.md) и [../docs/TECHNICAL.md](../docs/TECHNICAL.md).

## Отложенные события (`src/Event/Dispatcher.php`)

- **Имя события при доставке берётся из имени КАТАЛОГА, а не из объекта** —
  `Dispatcher.php:73`: `pathinfo($file->getPath(), PATHINFO_BASENAME)`. Переименуешь
  каталог под `cache/dev/events/` — сломаешь маршрутизацию события.
- **`unserialize` без `allowed_classes`** — `Dispatcher.php:74`. Источник — файлы в
  `app/cache/dev/events/`. Потенциальный PHP object injection, если кто-то может туда
  писать (модель угроз — в [context.md](context.md)).
- **Имя `.meta`-файла = `md5` сериализованной строки** — `Dispatcher.php:61`.
  Следствие 1: два идентичных события схлопываются в один файл (дедуп). Следствие 2:
  `sortByName()` (`:68`) сортирует по md5, т.е. порядок доставки НЕ хронологический.
- **`EVENTS_DIR = '/cache/dev/events/'`** (`Dispatcher.php:24`) — всегда `dev`, даже
  в prod-окружении. Не опечатка.

## Имена, версии, окружение

- **`root_dir` означает РАЗНОЕ в разных местах.** В `Dispatcher`/`Container` это `app/`
  (события пишутся в `app/cache/...`). В листенере `UpdateItself` это
  `%kernel.root_dir%/../`, т.е. корень проекта. Легко перепутать при правках.
- **`secret` генерится через `rand()`** — `src/Composer/SecretKey.php:24`,
  криптографически слабо (S3).
- **Версионный рассинхрон** — ветка `2.x`, `composer.json: 0.3.29`,
  `CHANGELOG.md: 0.3.16`, `branch-alias: dev-master → 0.3.x-dev`. Не доверяй одному
  источнику версии (B3).
- **Стек EOL + `minimum-stability: dev`** — PHP ≥5.4, Symfony ~2.7, Guzzle 3.9,
  PHPUnit ^4.8. `composer install` на современном PHP, скорее всего, не пройдёт.
  `dev`-стабильность намеренна (тянет dev-версии плагинов `anime-db/*`).
- **`git status` шумит правами 755→644** на `AnimeDB` и `app/console` — это монтаж
  WSL/Windows, не правки содержимого. НЕ коммитить.
- **Windows-вывод консоли перекодируется в `CP866`** — `Console\Output\Windows`
  (`src/Console/Output/Windows.php`). В самом репо класс не инстанцируется (только
  тест) — это компонент для внешнего консольного враппера.
