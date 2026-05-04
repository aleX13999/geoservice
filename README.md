#### Импорт тестовых данных в бд
```
docker compose exec -T mysql mysql -u root -proot --default-character-set=utf8mb4 geo < geo_point.sql
docker compose exec -T mysql mysql -u root -proot --default-character-set=utf8mb4 geo < geo_zone.sql
docker compose exec -T mysql mysql -u root -proot --default-character-set=utf8mb4 geo < geo_zone_point.sql
```

#### Запуск команды чтения и обработки данных

```
docker compose exec geo-php-fpm php artisan geo-handle
```

#### Запуск команды чтения и обработки данных из бд по расписанию cron

```
docker compose exec geo-php-fpm php artisan schedule:work
```

