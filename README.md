## Описание проекта
Обработка сырых гео-данных с мобильных устройств: построение треков, определение геозон, расчёт дистанций и сегментов.

---

## Технологии
- PHP 8.3+, Laravel 13
- MySQL с нестандартным портом (настройка через Docker)
- Docker и Docker Compose для разработки и развертывания
- Библиотека Leaflet
---

## Быстрый старт

### Требования

- Git, Docker и Docker Compose должны быть установлены на вашей машине.

### Шаги запуска

1. Клонируйте репозиторий с проектом:

```shell
git clone https://github.com/aleX13999/geoservice.git
cd <папка_проекта>
```

2. Создайте файл окружения `.env` скопировав его из файла `.env.example`

3. Отредактируйте `.env`, укажите необходимые настройки (под Docker):
```dotenv
NGINX_HOST=127.0.0.1
NGINX_HTTP_PORT=8081

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=root
DB_PASSWORD=

MYSQL_HOST=127.0.0.1
MYSQL_PORT=
MYSQL_ROOT_PASSWORD=
MYSQL_DATABASE=
MYSQL_USER=root
MYSQL_PASSWORD=
MYSQL_SERVER_VERSION=8.0

L5_SWAGGER_GENERATE_ALWAYS=true

API_KEY=
```

4. Запустите контейнеры Docker:

```shell
docker compose up -d
```
5. Сгенерируйте ключ приложения 
```shell
php artisan key:generate
```

6. Установите зависимости composer:

```shell
docker exec -it geo-php-fpm composer install
```

7. Выполните миграции:

```shell
docker compose exec geo-php-fpm php artisan migrate
```
8. Добавьте в БД тестовое устройство:
```shell
docker exec -it geo-php-fpm php artisan db:seed
```

#### Импорт тестовых данных в бд
```
docker compose exec -T mysql mysql -u {DB_USER} -p{DB_PASSWORD} --default-character-set=utf8mb4 {DB_DATABASE} < /path/to/geo_point.sql
docker compose exec -T mysql mysql -u {DB_USER} -p{DB_PASSWORD} --default-character-set=utf8mb4 {DB_DATABASE} < /path/to/geo_zone.sql
docker compose exec -T mysql mysql -u {DB_USER} -p{DB_PASSWORD} --default-character-set=utf8mb4 {DB_DATABASE} < /path/to/geo_zone_point.sql
```
Где:
*DB_USER*, *DB_PASSWORD* - имя пользователя и пароль базы данных, *DB_DATABASE* - имя базы данных

#### Запуск команды чтения и обработки данных

```
docker compose exec geo-php-fpm php artisan geo:handle
```

#### Запуск команды чтения и обработки данных из бд по расписанию cron

```
docker compose exec geo-php-fpm php artisan schedule:work
```

