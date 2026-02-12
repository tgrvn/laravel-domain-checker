# Domain Health Monitor

Веб-приложение для мониторинга доступности доменов. Автоматические проверки по расписанию, история проверок, уведомления при смене статуса (домен упал / восстановился).

## Стек

- **PHP 8.5** + **Laravel 11**
- **PostgreSQL 18**
- **Nginx**
- **Docker Compose** (5 контейнеров: nginx, php, postgres, scheduler, queue)
- **Tailwind CSS** (CDN)

## Архитектура

```
┌─────────────┐    каждые 60 сек    ┌──────────────────────┐
│  Scheduler  │───────────────────▸ │ DispatchDomainChecksJob│
└─────────────┘                     └──────────┬───────────┘
                                               │ chunkById(100)
                                               ▼
                                    ┌──────────────────────┐
                                    │   CheckDomainJob (x N)│──▸ Queue
                                    └──────────┬───────────┘
                                               │
                                    ┌──────────▼───────────┐
                                    │  DomainCheckService   │
                                    │  - HTTP GET/HEAD      │
                                    │  - замер response_time│
                                    │  - сохранение в БД    │
                                    │  - детекция смены     │
                                    │    статуса             │
                                    │  - уведомление user   │
                                    └──────────────────────┘
```

**Двухуровневая Job-архитектура:**
- **Tier 1 (Dispatcher)** — `DispatchDomainChecksJob` запускается scheduler каждую минуту, обходит домены чанками и диспатчит проверки для тех, у кого подошёл интервал
- **Tier 2 (Worker)** — `CheckDomainJob` выполняет HTTP-проверку одного домена, `$tries = 1` (неуспешная проверка — это данные, а не ошибка)

## Быстрый старт

### 1. Клонировать репозиторий

```bash
git clone <repo-url>
cd laravel
```

### 2. Настроить окружение

```bash
cp src/.env.example src/.env
```

Отредактировать `src/.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

### 3. Собрать и запустить

```bash
make build
```

Это поднимет 5 контейнеров:

| Контейнер | Назначение |
|-----------|------------|
| `nginx` | Веб-сервер, порт 8080 |
| `php` | PHP-FPM для обработки запросов |
| `postgres` | База данных |
| `scheduler` | Запуск `schedule:run` каждые 60 секунд |
| `queue` | Обработка фоновых задач (проверки доменов) |

### 4. Установить зависимости и мигрировать

```bash
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate
```

### 5. Открыть приложение

```
http://localhost:8080
```

## Makefile команды

| Команда | Описание |
|---------|----------|
| `make up` | Запустить контейнеры |
| `make build` | Пересобрать и запустить контейнеры |
| `make down` | Остановить контейнеры |
| `make php` | Bash-сессия в PHP-контейнере |
| `make postgres` | psql-сессия в PostgreSQL |

## Функциональность

### Управление доменами

- Создание / редактирование / удаление доменов
- При создании домена автоматически создаются настройки мониторинга

### Настройки мониторинга (per domain)

| Параметр | Диапазон | По умолчанию |
|----------|----------|-------------|
| Интервал проверки | 1 — 1440 мин | 5 мин |
| Таймаут запроса | 1 — 30 сек | 10 сек |
| HTTP метод | GET / HEAD | GET |
| Автопроверки | вкл / выкл | вкл |

Настройки встроены в форму редактирования домена.

### Проверки доменов

- **Автоматические** — scheduler + queue, по расписанию согласно интервалу
- **Ручные** — кнопка "Проверить" в списке доменов
- **История** — страница логов с таблицей: статус, HTTP код, время ответа, ошибка, дата

### Уведомления

- Колокольчик в навбаре с бейджем количества непрочитанных
- Выпадающий дропдаун со списком уведомлений (AJAX)
- **DomainDownNotification** — домен стал недоступен
- **DomainUpNotification** — домен снова доступен
- Отправляются только при **смене статуса** (up → down, down → up)
- Кнопки "Прочитано" / "Прочитать все"

### Часовые пояса

Все даты хранятся в UTC. На клиенте автоматически конвертируются в локальный часовой пояс браузера.

## Структура проекта

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DomainController.php
│   │   │   ├── DomainCheckController.php
│   │   │   ├── NotificationController.php
│   │   │   └── HomeController.php
│   │   └── Requests/Domain/
│   │       ├── StoreDomainRequest.php
│   │       ├── UpdateDomainRequest.php
│   │       └── DomainIndexRequest.php
│   ├── Jobs/
│   │   ├── DispatchDomainChecksJob.php    # Tier 1 — dispatcher
│   │   └── CheckDomainJob.php             # Tier 2 — worker
│   ├── Models/
│   │   ├── Domain.php
│   │   ├── DomainCheck.php
│   │   ├── DomainCheckSetting.php
│   │   └── User.php
│   ├── Notifications/
│   │   ├── DomainDownNotification.php
│   │   └── DomainUpNotification.php
│   ├── Policies/
│   │   └── DomainPolicy.php
│   ├── Repositories/
│   │   ├── DomainRepository.php
│   │   ├── DomainCheckRepository.php
│   │   └── NotificationRepository.php
│   └── Services/
│       ├── AuthService.php
│       ├── DomainService.php
│       ├── DomainCheckService.php
│       └── NotificationService.php
├── database/migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2024_02_12_000000_create_domains_table.php
│   ├── 2024_02_12_000001_create_domain_check_settings_table.php
│   ├── 2024_02_12_000002_create_domain_checks_table.php
│   ├── 2024_02_12_000003_add_last_status_to_domains_table.php
│   └── 2024_02_12_000004_create_notifications_table.php
└── resources/views/
    ├── layout.blade.php
    ├── auth/
    ├── domains/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   └── checks.blade.php
    └── ...
```

## Роуты

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/` | Редирект на домены или логин |
| GET | `/register` | Форма регистрации |
| POST | `/register` | Регистрация |
| GET | `/login` | Форма входа |
| POST | `/login` | Вход |
| POST | `/logout` | Выход |
| GET | `/domains` | Список доменов |
| GET | `/domains/create` | Форма создания домена |
| POST | `/domains` | Создать домен |
| GET | `/domains/{id}/edit` | Форма редактирования + настройки мониторинга |
| PUT | `/domains/{id}` | Обновить домен + настройки |
| DELETE | `/domains/{id}` | Удалить домен |
| GET | `/domains/{id}/checks` | История проверок |
| POST | `/domains/{id}/checks` | Ручная проверка |
| GET | `/notifications` | Список уведомлений (JSON) |
| PATCH | `/notifications/{id}/read` | Отметить прочитанным |
| POST | `/notifications/read-all` | Прочитать все |

## Оптимизации

- **chunkById(100)** — обход доменов порциями, не грузит все в память
- **Денормализация** `last_check_success` / `last_checked_at` на таблице `domains` — нет JOIN для отображения списка
- **Составные индексы** `[domain_id, checked_at]`, `[domain_id, is_success]` — быстрые запросы по логам
- **$tries = 1** — неудачная проверка это данные, не ретраим
- **Уведомления без ShouldQueue** — уже внутри queued job

## Схема БД

```
users
├── id
├── name
├── email
├── password
└── timestamps

domains
├── id
├── user_id (FK → users, cascade)
├── domain (unique)
├── last_check_success (nullable)
├── last_checked_at (nullable)
└── timestamps

domain_check_settings
├── id
├── domain_id (FK → domains, unique, cascade)
├── check_interval_minutes (default 5)
├── request_timeout_seconds (default 10)
├── check_method (default GET)
├── auto_checks_enabled (default true)
└── timestamps

domain_checks
├── id
├── domain_id (FK → domains, cascade)
├── is_success
├── status_code (nullable)
├── response_time_ms (nullable)
├── error_message (nullable)
├── checked_at
└── timestamps

notifications (Laravel standard)
├── id (uuid)
├── type
├── notifiable_type + notifiable_id
├── data (json)
├── read_at (nullable)
└── timestamps
```
