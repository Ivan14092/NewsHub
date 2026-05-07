FROM php:8.4-fpm

# 1. Системні пакети
RUN apt-get update && apt-get install -y \
    git unzip nginx libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# КРИТИЧНИЙ МОМЕНТ:
# Спочатку копіюємо ТІЛЬКИ файли залежностей
COPY composer.json composer.lock ./

# Встановлюємо їх ПЕРЕД копіюванням коду проекту
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# ТІЛЬКИ ТЕПЕР копіюємо решту проекту
COPY . .

# Примусово оновлюємо автозавантажувач, щоб він побачив App\Kernel тощо
RUN composer dump-autoload --optimize --no-dev

# Права та Nginx
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]