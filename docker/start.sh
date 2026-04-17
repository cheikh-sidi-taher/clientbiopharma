#!/bin/sh
set -e

cd /var/www

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist
fi

php artisan key:generate --force || true
php artisan migrate --force || true

chown -R www-data:www-data storage bootstrap/cache || true

exec php-fpm
