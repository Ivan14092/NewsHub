#!/bin/bash
set -e

echo "Starting PHP-FPM..."
php-fpm -D

# Чекаємо поки PHP-FPM запуститься
sleep 2

echo "Starting Nginx..."
exec nginx -g 'daemon off;'