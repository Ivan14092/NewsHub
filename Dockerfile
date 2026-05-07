FROM php:8.4-fpm

# Системні залежності
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    nginx \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 1. Копіюємо ВЕСЬ код
COPY . .

# 2. Очищуємо та встановлюємо залежності ПРЯМО ТУТ
# Це створить правильний vendor/autoload_runtime.php
RUN rm -rf vendor && composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 3. Права доступу
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

# 4. Налаштування Nginx (вже з правкою localhost)
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/conf.d/default.conf

# 5. Скрипт запуску
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]