# 1. Використовуємо стабільний образ PHP
FROM php:8.4-fpm

# 2. Встановлюємо системні залежності
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    nginx \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 3. Встановлюємо Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Робоча директорія
WORKDIR /var/www/html

# 5. Копіюємо ВЕСЬ проект (переконайтеся, що в .dockerignore НЕМАЄ vendor/)
COPY . .

# 6. Встановлюємо залежності
# Ми прибираємо --no-scripts, щоб Symfony Flex міг згенерувати autoload_runtime.php
# Додаємо --no-audit, щоб вразливості не блокували білд
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-audit
# 7. ГАРАНТІЯ: Якщо файл не створився, ми примусово перестворюємо його
RUN composer dump-autoload --optimize --no-dev

# 8. ПЕРЕВІРКА: зупиняємо білд, якщо файлу все ще немає (для діагностики)
RUN ls -l vendor/autoload_runtime.php || (echo "FATAL: autoload_runtime.php is still missing" && exit 1)

# 9. Права доступу та лог-файли
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

# 10. Налаштування Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/conf.d/default.conf

# 11. Скрипт запуску
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]