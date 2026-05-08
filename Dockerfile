FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    nginx \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN composer dump-autoload --classmap-authoritative --no-dev

RUN ls -l vendor/autoload_runtime.php || (echo "FATAL: autoload_runtime.php missing" && exit 1)

RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/conf.d/default.conf

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]