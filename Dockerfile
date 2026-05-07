FROM php:8.4-fpm

# ... (інсталяція системних пакетів) ...

WORKDIR /var/www/html

# 1. Спочатку копіюємо ТІЛЬКИ файли конфігурації
COPY composer.json composer.lock ./

# 2. Встановлюємо залежності (вони залишаться в шарі Docker)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-dev