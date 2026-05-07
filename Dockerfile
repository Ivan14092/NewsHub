FROM php:8.4-fpm

# 1. Системні пакети
RUN apt-get update && apt-get install -y \
    git unzip nginx libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 3. Копіюємо ВЕСЬ проект
COPY . .

# 4. Видаляємо vendor, якщо він випадково скопіювався порожнім, і ставимо все чисто
RUN rm -rf vendor && composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 5. КРИТИЧНА ПЕРЕВІРКА: якщо файлу немає, білд впаде з помилкою прямо зараз
RUN ls -l vendor/autoload_runtime.php || (echo "CRITICAL ERROR: autoload_runtime.php NOT FOUND" && exit 1)

# 6. Налаштування прав
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

# 7. Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]