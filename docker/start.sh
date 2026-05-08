#!/bin/bash
set -e

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "Starting PHP-FPM..."
php-fpm -D

sleep 2

echo "Starting Nginx..."
exec nginx -g 'daemon off;'