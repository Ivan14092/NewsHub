FROM php:8.4-fpm

# Встановлення системних залежностей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    nginx \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Встановлення Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копіюємо тільки файли залежностей для кешування шарів
COPY composer.json composer.lock ./

# Встановлюємо залежності без скриптів (вони запустяться пізніше)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Копіюємо весь проект (використовуючи .dockerignore)
COPY . .

# Створюємо папки та права доступу (краще робити до dump-autoload)
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

# Тепер генеруємо фінальний автозавантажувач
RUN composer dump-autoload --optimize --no-dev

# Конфігурація Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/conf.d/default.conf

# Скрипт запуску
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]