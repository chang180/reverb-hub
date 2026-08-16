#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache || true

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -qE '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

if [ "$1" = "php-fpm" ]; then
    php artisan migrate --force --no-interaction
    php artisan db:seed --force --no-interaction || true
fi

exec docker-php-entrypoint "$@"
