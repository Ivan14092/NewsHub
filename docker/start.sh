#!/bin/bash
set -e

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "Setting up messenger transports..."
php bin/console messenger:setup-transports --env=prod

echo "Initial price update..."
php bin/console app:update-prices BTC --env=prod || true
php bin/console app:update-prices USD --env=prod || true

echo "Fixing permissions..."
mkdir -p var/cache/prod/easyadmin
chown -R www-data:www-data var/
chmod -R 777 var/

echo "Starting Worker in background..."
(
    while true; do
        php bin/console messenger:consume async scheduler_prices --time-limit=3600 --env=prod -vv 2>&1 || echo "Worker exited with error $?"
        echo "Worker stopped, restarting in 5 seconds..."
        sleep 5
    done
) &

echo "Starting PHP-FPM..."
php-fpm -D

sleep 2

echo "Starting Nginx..."
exec nginx -g 'daemon off;'