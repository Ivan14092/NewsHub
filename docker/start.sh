#!/bin/bash
set -e

#echo "Clearing cache..."
#php bin/console cache:clear --env=prod --no-debug

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g 'daemon off;'
