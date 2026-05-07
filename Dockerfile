FROM php:8.2-fpm

# Встановлення залежностей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    nginx \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && apt-get clean

# Встановлення Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копіюємо composer файли спочатку (кешування шарів)
COPY composer.json composer.lock ./

# Встановлення залежностей без dev
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Копіюємо решту файлів
COPY . .

# Генеруємо autoload після копіювання всього коду
RUN composer dump-autoload --optimize

# Nginx конфіг
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN sed -i 's/fastcgi_pass php:9000/fastcgi_pass 127.0.0.1:9000/' /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
RUN rm -f /etc/nginx/conf.d/default.conf

# Права на папки
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

# Скрипт запуску
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]