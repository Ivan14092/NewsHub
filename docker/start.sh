#!/bin/bash
set -e

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "Loading fixtures..."
php bin/console doctrine:fixtures:load --no-interaction --env=prod

echo "Fixing permissions..."
mkdir -p var/cache/prod/easyadmin
chown -R www-data:www-data var/
chmod -R 777 var/

echo "Starting PHP-FPM..."
php-fpm -D

sleep 2

echo "Starting Nginx..."
exec nginx -g 'daemon off;'