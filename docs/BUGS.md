---
title: Найденные баги и уязвимости Anime DB
tags: [audit, anime-db, bugs, security]
updated: 2026-06-18
---

# Anime DB — баги и уязвимости

> Конкретные дефекты с местоположением и рекомендациями. Меры по улучшению в
> целом — [RECOMMENDATIONS.md](RECOMMENDATIONS.md). Сводка — [AUDIT.md](AUDIT.md),
> механика — [TECHNICAL.md](TECHNICAL.md).
>
> Серьёзность: 🔴 критично · 🟠 высоко · 🟡 средне/низко.

## Сводная таблица

| ID        | Тип          | Серьёзность  | Где                 | Кратко                                                       |
|-----------|--------------|--------------|---------------------|--------------------------------------------------------------|
| [B1](#b1) | Баг          | 🔴           | `UpdateItself.php`  | потеря разделителя/структуры путей при копировании источника |
| [S1](#s1) | Безопасность | 🔴           | `UpdateCommand.php` | самообновление без проверки подписи/хеша                     |
| [S2](#s2) | Безопасность | 🔴           | `parameters.yml`    | монитор скачивается по HTTP (MITM → RCE)                     |
| [S3](#s3) | Безопасность | 🟠           | `SecretKey.php`     | `secret` через `rand()`                                      |
| [S4](#s4) | Безопасность | 🟠           | `app_dev.php`       | `umask(0000)` + профайлер открыт для LAN                     |
| [B2](#b2) | Баг          | 🟠           | `GitHub.php`        | нет обработки ошибок GitHub API                              |
| [B3](#b3) | Баг/дизайн   | 🟠           | `GitHub.php`        | самообновление застряло на `<1.0.0`                          |
| [B4](#b4) | Баг          | 🟡           | `Firewall.php`      | IPv6 ULA `fd00::/8` не распознаётся                          |
| [S5](#s5) | Безопасность | 🟡           | `Dispatcher.php`    | `unserialize()` файлов кеша                                  |
| [B5](#b5) | Баг          | 🟡           | `UpdateItself.php`  | `md5_file()` по несуществующему файлу                        |
| [B6](#b6) | Баг          | 🟡           | `StaticFiles.php`   | нет confinement в `web/` (defense-in-depth)                  |
| [B7](#b7) | Баг          | 🟡           | `UpdateCommand.php` | объявлен `return bool`, ничего не возвращает                 |

---

## 🔴 Критичные

### B1. Потеря разделителя и структуры каталогов при копировании исходников {#b1}

**Файл:** `src/Event/Listener/UpdateItself.php`, метод
`onAppDownloadedMergeAppSource()`.

```php
foreach ($finder as $file) {
    /* @var $file SplFileInfo */
    $this->fs->copy($file->getRealpath(), $event->getPath().$file->getFilename());
}
```

**Проблема:** два дефекта сразу:

1. **Нет разделителя** между `$event->getPath()` и `$file->getFilename()`.
   Если `getPath()` = `/tmp/anime-db`, а файл — `anime.db`, цель будет
   `/tmp/anime-dbanime.db`, а не `/tmp/anime-db/app/Resources/anime.db`.
2. **Теряется относительный подкаталог** — берётся только `getFilename()`, поэтому
   все файлы из `app/DoctrineMigrations/` и `app/Resources/` (включая вложенные)
   сваливаются в одно место с конфликтами имён.

Сравните с корректным `onAppDownloadedMergeConfigs()`, который оперирует полными
путями `$this->root_dir.$file`.

**Последствие:** самообновление **повреждает** перенос БД и миграций — данные
пользователя (SQLite-коллекция) и схема могут не доехать до новой версии.

**Рекомендация:** вычислять относительный путь от корня и сохранять структуру:

```php
$base = $this->root_dir; // .../ (с завершающим слешем)
foreach ($finder as $file) {
    $relative = ltrim(str_replace($base, '', $file->getRealpath()), '/\\');
    $this->fs->copy($file->getRealpath(), rtrim($event->getPath(), '/').'/'.$relative);
}
```

(плюс убедиться, что `Finder` отдаёт пути относительно корня приложения, а не
двух разных `->in()`).

---

### S1. Самообновление без проверки подлинности дистрибутива {#s1}

**Файл:** `src/Command/UpdateCommand.php` → `doUpdateItself()`.

Приложение скачивает zip по `zipball_url` из ответа GitHub и **перезаписывает
собственные `src/` и `app/`** без какой-либо проверки контрольной суммы или
цифровой подписи:

```php
$package->setDistUrl($tag['zipball_url']);
$composer->download($package, $target);
...
$this->rewriting($target); // rm -rf src/ app/ + mirror нового кода
```

**Последствие:** любой, кто способен подменить ответ (компрометация канала,
DNS/TLS-проблема, изменённый параметр `anime_db.github.api`, репозиторий-зеркало),
получает **выполнение произвольного кода** на машине пользователя при следующем
`animedb:update`.

**Рекомендация:** публиковать и проверять подпись/хеш релиза (GPG-подпись тега
или SHA-256 артефакта из доверенного манифеста); строго пинить `https://api.github.com`;
не позволять переопределять API-хост из недоверенных источников.

---

### S2. Загрузка монитора по незашифрованному HTTP {#s2}

**Файл:** `src/Resources/config/parameters.yml`

```yaml
anime_db.monitor: 'http://anime-db.org/download/monitor_1.0.zip'
```

Используется в `UpdateItself::onAppDownloadedMergeBinRun()` — скачивается zip,
**распаковывается и становится исполняемым** `AnimeDB.exe` (Windows-монитор в трее).

**Последствие:** классический MITM на plain HTTP → подмена исполняемого файла →
RCE на Windows-машине пользователя.

**Рекомендация:** заменить на `https://`, добавить проверку хеша архива монитора.

---

## 🟠 Высокие

### S3. Криптографически слабый секретный ключ {#s3}

**Файл:** `src/Composer/SecretKey.php`

```php
$secret .= $chars[rand(0, strlen($chars) - 1)];
```

`rand()` не является криптостойким и предсказуем. Сгенерированный `secret`
используется Symfony для CSRF-токенов, подписи URI, защиты сессий.

**Рекомендация:**

```php
public static function generate()
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $secret = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < self::KEY_LENGTH; ++$i) {
        $secret .= $chars[random_int(0, $max)]; // PHP 7+; для 5.x — paragonie/random_compat
    }
    return $secret;
}
```

(проект таргетит 5.4 — подключить `paragonie/random_compat` либо
`openssl_random_pseudo_bytes`).

### S4. `app_dev.php` без ограничения по IP + `umask(0000)` {#s4}

**Файл:** `web/app_dev.php`

```php
umask(0000); // создаёт файлы доступными на запись всем
$kernel = new AppKernel('dev', true); // профайлер, wdt, debug
```

В отличие от стандартного Symfony-скелета, здесь **снят guard, ограничивающий
доступ к `app_dev.php` только с `127.0.0.1`**. `app/router.php` отдаёт
`/app_dev.php` напрямую. Слушатель `Firewall` хоть и ограничивает приватной сетью,
но это всё равно **вся локальная сеть** — профайлер (`/_profiler`, `/_wdt`),
трассировки, конфигурация становятся доступны соседям по LAN. `umask(0000)`
дополнительно создаёт world-writable файлы кеша.

**Рекомендация:** убрать `umask(0000)`; вернуть IP-guard в `app_dev.php` (только
loopback) либо вовсе не поставлять `app_dev.php` в дистрибутиве.

### B2. Нет обработки ошибок ответа GitHub API {#b2}

**Файл:** `src/Client/GitHub.php`

```php
$response = $this->client->get('repos/'.$repository.'/tags')->send();
return json_decode($response->getBody(true), true);
```

При rate-limit/4xx/5xx GitHub возвращает объект вида `{"message": "...",
"documentation_url": "..."}`. Тогда `getLastRelease()` итерирует по нему как по
списку тегов и обращается к `$tag['name']` на строке — предупреждения, неверная
логика, на PHP 8 — фатал (но проект таргетит ≤7.1). Также нет тайм-аутов и
User-Agent (GitHub требует UA).

**Рекомендация:** проверять HTTP-код, валидировать, что тело — список; задать
`User-Agent` и тайм-ауты Guzzle; ловить исключения и выдавать понятную ошибку.

### B3. Самообновление не выходит за пределы `0.x` {#b3}

**Файл:** `src/Client/GitHub.php` → `getLastRelease()`

```php
version_compare($version, '1.0.0', '<') // v1.0.0 is BC
```

Фильтр отбрасывает любые теги ≥ 1.0.0. Рабочая ветка разработки — `2.x`, то есть
механизм самообновления **архитектурно не сможет** доставить пользователя на
актуальную линию. Если это сознательное BC-решение — его нужно задокументировать;
если нет — это блокирующий дефект апдейтера.

**Рекомендация:** определить политику версий (см. [RECOMMENDATIONS.md](RECOMMENDATIONS.md#версионирование));
сделать верхнюю границу конфигурируемой или убрать.

---

## 🟡 Средние / низкие

### B4. IPv6 ULA `fd00::/8` не распознаётся как локальная сеть {#b4}

**Файл:** `src/Event/Listener/Request/Firewall.php`

```php
return strpos($addr, 'fc00::') === 0;
```

ULA-диапазон — `fc00::/7`, то есть включает и `fd00::…` (а на практике почти все
ULA-адреса — именно `fd…`). Проверка ловит только литеральный префикс `fc00::`.

**Рекомендация:** проверять первый октет на принадлежность `fc00::/7`
(`fc` или `fd` в начале), например через `inet_pton` и битовую маску.

### S5. `unserialize()` содержимого файлов событий {#s5}

**Файл:** `src/Event/Dispatcher.php` → `shippingDeferredEvents()`

```php
unserialize(file_get_contents($file->getPathname()))
```

PHP object injection, если злоумышленник может записать `.meta` в
`app/cache/dev/events/`. Локально-ограниченный риск (нужен доступ к ФС), но это
антипаттерн.

**Рекомендация:** перейти на безопасную сериализацию (JSON DTO) либо
`unserialize($data, ['allowed_classes' => [...]])` (PHP 7+).

### B5. `md5_file()` по потенциально несуществующему файлу {#b5}

**Файл:** `src/Event/Listener/UpdateItself.php` → `onAppDownloadedMergeBinService()`

```php
$old_file = $this->root_dir.'AnimeDB';
if (!$this->fs->exists($old_file)) {
    $old_file = $this->root_dir.'bin/service';
}
$new_file = $event->getPath().'/AnimeDB';
if (is_readable($new_file) && md5_file($old_file) != md5_file($new_file)) {
```

Если ни `AnimeDB`, ни `bin/service` не существуют, `md5_file($old_file)` бросит
warning и вернёт false. Аналогично в `onAppDownloadedMergeAppSource()`
`bootstrap.php.cache` копируется без проверки существования.

**Рекомендация:** проверять `is_readable($old_file)` перед хешированием.

### B6. `StaticFiles` не ограничивает выдачу каталогом `web/` {#b6}

**Файл:** `src/Event/Listener/Request/StaticFiles.php`

```php
$file = $request->getScriptName() == '/app_dev.php' ? $request->getPathInfo() : $request->getScriptName();
if (is_file($file = $this->root_dir.'/../web'.$file)) {
    ... file_get_contents($file) ...
}
```

Пути нормализуются Symfony/встроенным сервером, поэтому прямой traversal
маловероятен, но **нет явного `realpath`-confinement** в `web/` — defense-in-depth
отсутствует. Также `mime_content_type()` может быть недоступен/вернуть false.

**Рекомендация:** проверять, что `realpath($file)` начинается с `realpath(web/)`;
ограничить выдачу белым списком расширений.

### B7. `UpdateCommand::execute()` не возвращает значение {#b7}

**Файл:** `src/Command/UpdateCommand.php`

PHPDoc обещает `@return bool`, но метод ничего не возвращает (значит, exit-код
команды всегда 0, даже при ошибке обновления зависимостей — там лишь печатается
`<error>`). Аналогично `DeliverEventsCommand::execute()`.

**Рекомендация:** возвращать `0`/`1` (или константы Command) в зависимости от
результата `getInstaller()->run()`.

---

## Не-баги, но стоит знать (гочи)

- **`git status` показывает изменения в `AnimeDB` и `app/console`** — это лишь
  смена режима `755 → 644` из-за монтирования WSL/Windows, а не правки содержимого.
  Не коммитить.
- **`composer.lock` намеренно удаляется** в `Composer::reload()` — это не баг.
- **`vendor_config.yml`, `routing.yml`, `bundles.php`, `parameters.yml`,
  `app/DoctrineMigrations/`** генерируются автоматически — правки руками затрутся.
