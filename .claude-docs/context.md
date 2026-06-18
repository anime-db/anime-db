---
title: Context — операционный контекст и модель угроз
tags: [memory/repo, context, anime-db, security]
updated: 2026-06-18
---

# Операционный контекст

## Кто и как вызывает этот сервис

- Anime DB — **локальное десктоп-приложение**, а не публичный веб-сервис. Запускается
  как Symfony-сервер на `127.0.0.1:56780`.
- Старт: bin-скрипт `AnimeDB` (Unix) или `AnimeDB_Run.vbs` (Windows) + внешняя
  утилита `monitor` в system tray.
- БД — локальный **SQLite** (`app/Resources/<database_path>`).
- Доступ ограничивается на уровне приложения:
  - `Event\Listener\Request\Firewall` (priority −255, `services.yml`) — пускает только
    localhost/локальную сеть, иначе 403 + `stopPropagation`. Блокирует при наличии
    `HTTP_CLIENT_IP` / `HTTP_X_FORWARDED_FOR` (признак прокси).
  - `Event\Listener\Request\StaticFiles` (priority −256) — отдаёт статику из `web/`.
- Расширяется плагинами-пакетами `anime-db/*` (catalog, filler-bundle'ы), которые
  при установке через Composer сами вписываются в kernel/routing/config (см.
  Composer-цикл в [../docs/TECHNICAL.md](../docs/TECHNICAL.md)).

## Модель угроз

Приложение спроектировано под локальный запуск одним пользователем. Известные риски
(детали и фиксы — [../docs/BUGS.md](../docs/BUGS.md)):

| Риск                                                                                                                        | Где                                        | Статус |
|-----------------------------------------------------------------------------------------------------------------------------|--------------------------------------------|--------|
| Самообновление без проверки подписи/чексуммы скачанного zip (S1)                                                            | `UpdateCommand` / `Composer::download`     | открыт |
| `monitor` качается по HTTP — возможен MITM/подмена бинаря (S2)                                                              | `UpdateItself::onAppDownloadedMergeBinRun` | открыт |
| `unserialize` отложенных событий без `allowed_classes` — object injection, если кто-то пишет в `app/cache/dev/events/` (S5) | `Event/Dispatcher.php:74`                  | открыт |
| `secret` через `rand()` — криптографически слаб (S3)                                                                        | `src/Composer/SecretKey.php:24`            | открыт |
| `app_dev.php` с `umask(0000)` и открытым профайлером (S4)                                                                   | `web/app_dev.php`                          | открыт |

Граница доверия: приложение слушает только локально (Firewall-listener), поэтому
сетевая поверхность мала — НО самообновление выходит наружу (GitHub, HTTP-monitor)
без верификации, и это главный вектор.

## Легаси-решения и их статус

| Решение                                                                  | Статус                                                      |
|--------------------------------------------------------------------------|-------------------------------------------------------------|
| EOL-стек: PHP ≥5.4, Symfony ~2.7, Guzzle 3.9, PHPUnit ^4.8               | заморожен; `composer install` на современном PHP не пройдёт |
| `minimum-stability: dev`                                                 | намеренно — тянет dev-версии плагинов `anime-db/*`          |
| `composer.lock` удаляется в `Composer::reload()`                         | намеренная стратегия «последние совместимые версии»         |
| Апдейтер ограничен `< 1.0.0` («v1.0.0 is BC»)                            | проект застрял на ветке 0.x (B3); требует решения           |
| Версионный рассинхрон (branch `2.x` / `0.3.29` / CHANGELOG `0.3.16`)     | известен, не разрешён                                       |
| Doctrine-миграции в legacy-формате правятся на лету (`repackMigrations`) | работает, но хрупко                                         |
| `bundles.php` патчится текстовым парсингом (`Manipulator\Kernel`)        | работает, хрупко к форматированию                           |
| B1 — апдейтер уплощает пути при merge app source                         | баг, открыт (см. [gotchas.md](gotchas.md))                  |

## Ключевые правила работы (из [../CLAUDE.md](../CLAUDE.md))

- НЕ редактировать вручную автогенерируемые файлы: `app/bundles.php`,
  `app/config/{vendor_config,routing,parameters}.yml`, `app/DoctrineMigrations/*`.
- НЕ коммитить изменения прав `755→644` на `AnimeDB`/`app/console` (артефакт WSL).
- Перед правками самообновления — читать [../docs/BUGS.md](../docs/BUGS.md) и
  [../docs/TECHNICAL.md](../docs/TECHNICAL.md).
