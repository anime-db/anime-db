---
title: Глубокая техническая документация Anime DB
tags: [audit, anime-db, technical, deep-dive]
updated: 2026-06-18
---

# Anime DB — техническая документация ядра

> Глубокий разбор механики ядра. Обзорная структура — в [PROJECT.md](PROJECT.md),
> дефекты — в [BUGS.md](BUGS.md), сводка — в [AUDIT.md](AUDIT.md).

## Содержание

- [Жизненный цикл Composer](#жизненный-цикл-composer)
- [ScriptHandler](#scripthandler)
- [Система задач (Job/Container)](#система-задач)
- [Манипуляторы](#манипуляторы)
- [Отложенные события](#отложенные-события)
- [Самообновление приложения](#самообновление-приложения)
- [Проксирование миграций](#проксирование-миграций)
- [Совместимость версий](#совместимость-версий)
- [HTTP-слой и запуск](#http-слой)
- [DI и сервисы](#di-и-сервисы)
- [Тестирование](#тестирование)

## Жизненный цикл Composer

Главный архитектурный приём: **хуки Composer запускают операции Symfony**. В
`composer.json` секция `scripts` навешивает методы `ScriptHandler` на события
Composer:

| Событие Composer        | Что вызывается (по порядку)                                                                                                                                                |
|-------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `pre-install-cmd`       | `installConfig`                                                                                                                                                            |
| `post-install-cmd`      | `generateSecretKey` → buildParameters → buildBootstrap → `notifyProjectInstall` → `execJobs` → `migrateUp` → `deliverEvents` → `clearCache` → installAssets → `dumpAssets` |
| `pre-update-cmd`        | `backupDB` → `installConfig`                                                                                                                                               |
| `post-update-cmd`       | то же, что post-install, но с `notifyProjectUpdate`, `migrateDown` + `migrateUp`                                                                                           |
| `post-package-install`  | `packageInKernel` → `packageInRouting` → `packageInConfig` → `migratePackage` → `notifyPackage`                                                                            |
| `post-package-update`   | то же                                                                                                                                                                      |
| `pre-package-uninstall` | то же (но операции зарегистрируют Remove-задачи)                                                                                                                           |

Идея: события `post-package-*` лишь **регистрируют задачи** в `Container`, а
`post-install/update-cmd` → `execJobs` их **выполняет** уже после того, как
Composer обновил `vendor/`. Это важно: на момент `post-package-install` файлы
пакета ещё могут быть не на финальных местах, поэтому реальная работа отложена.

## ScriptHandler

`src/Composer/ScriptHandler.php` — статический фасад. Хранит singleton `Container`
и `root_dir` (по умолчанию `getcwd().'/app/'`).

Ключевые методы:

- **`installConfig()`** — создаёт пустые `vendor_config.yml`, `routing.yml`,
  `bundles.php`, если их нет. На Windows при наличии `bin/php/php.ini` поднимает
  `memory_limit` до 1G (через `PhpIni`-манипулятор).
- **`generateSecretKey()`** — `touch` parameters.yml, и если нет `secret`, кладёт
  `SecretKey::generate()`. ⚠️ См. [BUGS.md](BUGS.md#s3) — небезопасный генератор.
- **`packageIn{Kernel,Routing,Config}` / `migratePackage` / `notifyPackage`** —
  через `addJobByOperationType()` выбирают Add/Remove/Update-задачу в зависимости
  от типа операции Composer (`install`/`uninstall`/`update`).
- **`migrateUp()`** — если в `app/DoctrineMigrations` есть файлы `Version\d{14}*.php`,
  выполняет `repackMigrations()` (правит старый формат `getMigrationClass()` →
  `getMigration()`) и запускает `doctrine:migrations:migrate`.
- **`migrateDown()`** — пишет временный `migrations.yml` в
  `app/cache/dev/DoctrineMigrations/` и мигрирует «вниз» до версии `0`.
- **`backupDB()`** — копирует `app/Resources/anime.db` → `anime.db.bk`.
- **`clearCache()`** — принудительно сносит `app/cache/prod` (из-за зашифрованного
  контейнера), затем `cache:clear` для prod и dev.
- **`dumpAssets()`** — `assetic:dump --env=prod`.

Команды Symfony выполняются через `Container::executeCommand()` — порождается
дочерний PHP-процесс `php app/console <cmd>`.

## Система задач

`Composer\Job\Container` — реестр и исполнитель задач.

- Хранит задачи в `jobs[priority][]`, манипуляторы (lazy), путь к PHP.
- `addJob()` инжектит контейнер и `root_dir`, кладёт по приоритету, вызывает
  `register()` задачи (важно для Remove/Down — они снимают данные **до** удаления
  пакета).
- `execute()` сортирует по приоритету (`ksort`) и прогоняет.
- `executeCommand()` запускает `php app/console …` через `Symfony\Process`
  (`escapeshellarg` на путь PHP), таймаут по умолчанию 300 c.
- `getManipulator($name)` — фабрика манипуляторов по строковому имени
  (`composer`, `config`, `kernel`, `routing`, `php.ini`, `parameters`).

`Composer\Job\Job` (абстрактный) даёт задачам общую логику:

- Конструктор дополняет `extra` пакета ключами `anime-db-routing/config/bundle/
  migrations` (значения по умолчанию — пустые).
- **`getPackageBundle()`** — эвристика вывода FQCN бандла из имени пакета
  (`demo-vendor/foo-bar-bundle` → `\DemoVendor\Bundle\FooBarBundle\…`), с кешем
  найденного класса в `extra['anime-db-bundle']`. Пробует несколько вариантов
  имени (учёт того, что vendor может входить в имя бандла, как `knplabs/knp-menu-bundle`).
- **`getRoutingNodeName()`** — нормализует имя пакета в ключ узла routing.yml.
- `getPackageDir()` = `vendor/<name>/`.

### Add/Remove-задачи

- `Kernel\Add/Remove` → `Manipulator\Kernel` правит `bundles.php`.
- `Routing\Add` (наследник `BaseAddConfig`) ищет `Resources/config/routing.{yml,xml}`
  в пакете и подключает; **исключение** — `sensio/framework-extra-bundle`
  (его routing.xml содержит сервисы, а не роуты).
- `Config\Add` ищет `config.{yml,xml}` и добавляет в `imports`.
- `BaseAddConfig::getPackageConfig()` — ищет конфиг через `Finder`, исключая
  пути с `test` (кроме самих тестовых пакетов).

## Манипуляторы

Все наследуют `FileContent` (или `Yaml`/`PhpIni`). Принцип: прочитать файл →
изменить структуру → записать.

- **`Kernel`** — парсит `bundles.php` как текст (ищет `[` … `]`), хранит строки
  вида `new Foo\Bar\Bundle()`, добавляет/убирает. Проверяет, что бандл не «корневой»
  (уже в `AppKernel.php`).
- **`Yaml`** (`Config`, `Routing`, `Parameters`) — `Symfony\Yaml::parse/dump`.
  `Parameters` умеет `get/set/setParameters` по ключу в секции `parameters`.
- **`Composer`** — читает/пишет `composer.json` как JSON
  (`JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK`).
- **`PhpIni`** — строковый парсер ini, поддерживает повторяющиеся ключи (массив),
  конвертацию `1G/1M/1K` ↔ байты.

## Отложенные события

`Event\Dispatcher` (`src/Event/Dispatcher.php`):

```
dispatch($name, $event):
    dir = root_dir + '/cache/dev/events/' + $name + '/'
    file_put_contents(dir + md5(serialize($event)) + '.meta', serialize($event))

shippingDeferredEvents():
    Finder по events_dir, *.meta, sortByName
    для каждого файла:
        dispatcher->dispatch( basename(getPath()) , unserialize(file) )
        unlink(file)
```

Имя события восстанавливается из **имени каталога** (`basename` от `getPath()` =
имя подкаталога `events/<name>/`). Доставка идёт через настоящий Symfony
EventDispatcher (инжектится через `setDispatcherDriver`).

> ⚠️ `unserialize()` содержимого файла — потенциальный вектор object injection,
> если кто-то сможет писать в кеш-каталог. Локально-низкий риск, см.
> [BUGS.md](BUGS.md#s5).

## Самообновление приложения

`Command\UpdateCommand::execute()`:

1. Берёт сервисы `anime_db.composer` и `anime_db.client.github`.
2. `GitHub::getLastRelease('anime-db/anime-db')` — GET `repos/.../tags`, выбирает
   максимальный тег с версией `< 1.0.0` (см. ниже B3).
3. Сравнивает с текущей версией (`version_compare(...) == 1`).
4. Если новее — `doUpdateItself()`:
   - создаёт `Composer\Package\Package` с `distUrl = zipball_url`, тип `zip`;
   - качает в `sys_get_temp_dir().'/anime-db'` через `Composer::download()`;
   - читает `composer.json` нового пакета;
   - выпускает событие `anime_db.update_itself.downloaded` (**сразу**, через
     обычный `event_dispatcher`);
   - `rewriting($target)` — **удаляет** `src/` и `app/` старого приложения и
     `mirror`-ит новые файлы поверх корня;
   - выпускает отложенное событие `anime_db.update_itself.updated`.
5. Затем `Composer::getInstaller()->run()` обновляет зависимости.

`Event\Listener\UpdateItself` подписан на `downloaded` и выполняет 6 шагов слияния
(порядок задан в `services.yml`):

| Метод                                      | Что делает                                                                   |
|--------------------------------------------|------------------------------------------------------------------------------|
| `onAppDownloadedMergeComposerRequirements` | сливает `require` старого и нового `composer.json`                           |
| `onAppDownloadedMergeConfigs`              | копирует `parameters.yml`, `vendor_config.yml`, `routing.yml`, `bundles.php` |
| `onAppDownloadedMergeBinRun`               | Windows: подтягивает монитор (zip по HTTP!), переносит `config.ini`          |
| `onAppDownloadedMergeBinService`           | переносит параметры из старого `AnimeDB`-скрипта                             |
| `onAppDownloadedChangeAccessToFiles`       | `chmod 0755` на `AnimeDB` и `app/console` (не-Windows)                       |
| `onAppDownloadedMergeAppSource`            | копирует `bootstrap.php.cache`, миграции, Resources                          |

> 🔴 **Критика безопасности:** дистрибутив качается без проверки подписи/хеша
> (S1), монитор — по plain HTTP (S2). 🔴 **Баг:** `onAppDownloadedMergeAppSource`
> теряет разделитель пути (B1). Детали — [BUGS.md](BUGS.md).

## Проксирование миграций

Проблема: миграции живут в разных плагинах и могут зависеть друг от друга, поэтому
их нельзя прогонять «по пакету». Решение — **обёртки**:

- `Job\Migrate\Up` для каждого `VersionXXXX.php` плагина создаёт в
  `app/DoctrineMigrations/` PHP-файл-обёртку класса, наследующего `ProxyMigration`,
  который `require_once` подключает реальный файл из `vendor/` и в `getMigration()`
  возвращает экземпляр настоящей миграции.
- `ProxyMigration` (`src/DoctrineMigrations/ProxyMigration.php`) — ленивый прокси:
  делегирует `up/down/preUp/...` реальной миграции, пробрасывает контейнер
  (`ContainerAwareInterface`).
- `Job\Migrate\Down` в `register()` (до удаления пакета) копирует миграции во
  временный каталог `app/cache/dev/DoctrineMigrations/` и меняет namespace на
  `Application\Migrations`, чтобы выполнить откат уже после удаления исходников.

Так все миграции оказываются в одном namespace `Application\Migrations` и
сортируются глобально по timestamp.

## Совместимость версий

`Composer::getVersionCompatible()` превращает SemVer-с-суффиксом в сравнимую
числовую строку (`3.2.1-RC2` → `3.2.1.6.2`). Веса суффиксов:
`dev=1, patch=2, alpha=3, beta=4, stable=5, rc=6`. Регэксп принимает
`v?X.Y.Z(-suffixN)?`.

> ⚠️ `getLastRelease()` дополнительно фильтрует `version < 1.0.0` (комментарий
> «v1.0.0 is BC»). Поскольку публичных тегов ≥1.0 нет, а ветка разработки — `2.x`,
> самообновление **в принципе не выйдет за пределы линии 0.3.x** (B3).

## HTTP-слой

- **`app/router.php`** — для встроенного сервера: `/update.log` и `/app_dev.php`
  возвращаются как есть, остальное → `web/app.php`.
- **`web/app.php`** — боевой контроллер, `prod`, оборачивает ядро в `AppCache`
  (Symfony Reverse Proxy), опционально APC-автозагрузка.
- **`web/app_dev.php`** — dev, `umask(0000)`, **без ограничения по IP** (S4).
- **`Firewall`** (kernel.request, prio -255): пускает `127.0.0.1`, `::1`,
  `fe80::1` и приватные диапазоны IPv4 (`10/8`, `172.16/12`, `192.168/16`) и IPv6
  `fc00::`. Режет запросы с `HTTP_CLIENT_IP`/`HTTP_X_FORWARDED_FOR` (анти-прокси).
- **`StaticFiles`** (kernel.request, prio -256): если запрошенный путь существует
  в `web/`, отдаёт файл напрямую (`file_get_contents`), с ETag/Expires в prod.

## DI и сервисы

`AnimeDbAnimeDbExtension` грузит `Resources/config/parameters.yml` и `services.yml`.
Параметры:

```yaml
anime_db.monitor:    'http://anime-db.org/download/monitor_1.0.zip'   # ⚠ HTTP
anime_db.github.api: 'https://api.github.com/'
```

Ключевые сервисы (`src/Resources/config/services.yml`):

| Сервис                                       | Класс                                                      |
|----------------------------------------------|------------------------------------------------------------|
| `anime_db.listener.update`                   | `Event\Listener\UpdateItself` (6 подписок на `downloaded`) |
| `anime_db.event_dispatcher`                  | `Event\Dispatcher` (+ `setDispatcherDriver`)               |
| `anime_db.manipulator.{composer,parameters}` | манипуляторы                                               |
| `anime_db.zip`                               | `ZipArchive`                                               |
| `anime_db.client`                            | `Guzzle\Http\Client`                                       |
| `anime_db.client.github`                     | `Client\GitHub`                                            |
| `anime_db.composer`                          | `Composer\Composer`-обёртка                                |
| `anime_db.request.firewall` / `.static`      | слушатели запросов                                         |

## Тестирование

- PHPUnit `^4.8`, bootstrap `app/bootstrap.php.cache`, покрытие → `build/coverage-clover.xml`.
- Тесты в `src/Tests/` зеркалят `src/` (31 файл, ~5 075 строк).
- `TestCaseWritable` — базовый кейс для тестов, пишущих во временные файлы.
- CI: Travis (PHP 5.4–7.1), Scrutinizer, Coveralls, StyleCI (preset symfony +
  short_array_syntax, без phpdoc_align).

## Прочие технические заметки

- **Windows-вывод** (`Console\Output\Windows`) перекодирует сообщения в `CP866`
  через mbstring — иначе кириллица в cmd.exe ломается.
- **`reload()` обёртки Composer** удаляет `composer.lock` → форсирует обновление
  до последних совместимых версий компонентов.
- `minimum-stability: dev` — пакеты ставятся из dev-веток.
