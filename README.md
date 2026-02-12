# appApi (Laravel)

Сервис на Laravel для получения данных из удалённого API (заказы/продажи/склады/поставки) и сохранения результатов в локальную БД.  
Эндпоинты доступны по префиксу `/api/remote-api/*` и защищены ключом доступа.

---

## Содержание

- [Возможности](#возможности)
- [Стек](#стек)
- [Требования](#требования)
- [Установка и запуск](#установка-и-запуск)
- [Конфигурация](#конфигурация)
  - [Доступ к удалённому API](#доступ-к-удалённому-api)
  - [Ключ доступа к маршрутам](#ключ-доступа-к-маршрутам)
  - [База данных](#база-данных)
- [Маршруты API приложения](#маршруты-api-приложения)
- [Сохранение данных в БД](#сохранение-данных-в-бд)
- [Структура проекта](#структура-проекта)
- [Модели и таблицы](#модели-и-таблицы)

---

## Возможности

- Получение данных из удалённого API:
  - **Orders** (заказы)
  - **Sales** (продажи)
  - **Stocks** (остатки, только текущий день)
  - **Incomes** (поставки)
- Сохранение в MySQL через Eloquent (upsert: обновление/создание без дублей).
- Защита всех `remote-api` маршрутов middleware-ключом.

---

## Стек

- PHP + Laravel
- MySQL/MariaDB
- Laravel HTTP Client (`Http::...`) для запросов к удалённому API

---

## Требования

- PHP (рекомендуется 8.1+)
- Composer
- MySQL/MariaDB

---

## Установка и запуск

1) Установка зависимостей:
```bash
composer install
```

2) Подготовка окружения:
```bash
cp .env.example .env
php artisan key:generate
```

3) Настройка .env:
см. раздел Конфигурация

4) Миграции:
```bash
php artisan migrate
```

5) Очистка кешей (при необходимости):
```bash
php artisan optimize:clear
```

---

## конфигурация

### Доступ к удалённому API

Логика запросов находится в app/Services/RemoteApiService.php.
Параметры доступа (ключ, базовый URL и т.п.) настраиваются через .env (если вы добавляли их в проект).

### Ключ доступа к маршрутам

Маршруты /api/remote-api/* защищены middleware RemoteApiAccessKey.

В .env:
REMOTE_API_ACCESS_KEY=MySuperSecretKey123

Ключ можно передавать:

- заголовком: X-Access-Key: <key>
- или query параметром: ?access_key=<key>

При неверном ключе возвращается:

- HTTP 403
- JSON: { "error": "closed", "message": "Доступ закрыт" }

---

## База данных

### Доступ (phpMyAdmin)

- Адрес: https://golf.beget.com/phpMyAdmin/index.php
- Имя БД: cajkador_appapi
- Пользователь: cajkador_appApi
- Пароль: qt6Ah0c6mg!v

### Пример настроек .env

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cajkador_appapi
DB_USERNAME=cajkador_appApi
DB_PASSWORD=qt6Ah0c6mg!v
```

## Маршруты API приложения

Префикс: /api/remote-api
Все маршруты требуют ключ доступа (см. Ключ доступа к маршрутам).

### Orders

```text
GET /api/remote-api/orders
```

### Sales

```text
GET /api/remote-api/sales
```
### Stocks (текущий день)

```text
GET /api/remote-api/stocks
```

Контроллер подставляет dateFrom = today() и делает запрос в удалённый API.

### Incomes

```text
GET /api/remote-api/incomes
```

---

## Сохранение данных в БД

Сохранение реализовано через модели, каждая имеет методы:

- upsertFromApi(array $row)
- upsertManyFromApi(array $rows)

Upsert выполнен через updateOrCreate() + уникальные индексы в таблицах, чтобы повторная выгрузка не создавала дубли.

---

## Структура проекта

1. routes/api.php — маршруты remote-api (группа + middleware-ключ)
2. app/Http/Controllers/Remote/*Controller.php — контроллеры:
    - OrdersController
    - SalesController
    - StocksController
    - IncomesController
3. app/Services/RemoteApiService.php — клиент удалённого API
4. app/Http/Middleware/RemoteApiAccessKey.php — защита маршрутов по ключу
5. app/Models/* — модели таблиц:
    - RemoteOrder
    - RemoteSale
    - RemoteStock
    - RemoteIncome
6. database/migrations/* — миграции таблиц:
    - remote_orders
    - remote_sales
    - remote_stocks
    - remote_incomes

---

## Модели и таблицы

- remote_orders ↔ App\Models\RemoteOrder
- remote_sales ↔ App\Models\RemoteSale
- remote_stocks ↔ App\Models\RemoteStock
- remote_incomes ↔ App\Models\RemoteIncome

---

## Примечания по типам данным

- Денежные поля: decimal(14,2) (из API часто приходят строкой — сохраняются как строка).
- nm_id: bigInteger (signed), т.к. в источнике могут встречаться отрицательные значения.
- Для полей, которые иногда отсутствуют у API, применяется nullable() на уровне миграции.
