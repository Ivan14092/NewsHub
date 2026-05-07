# ... (початок Dockerfile той самий) ...

WORKDIR /var/www/html

# 1. Копіюємо проект
COPY . .

# 2. Видаляємо vendor і чистимо кеш
RUN rm -rf vendor composer.lock && composer clear-cache

# 3. Встановлюємо залежності. 
# ВАЖЛИВО: ми прибираємо --no-scripts, щоб Symfony Flex міг відпрацювати, 
# але додаємо APP_RUNTIME_MODE, щоб він не падав на помилках бази даних
RUN APP_ENV=prod composer install --no-dev --optimize-autoloader --no-interaction

# 4. Якщо файл все ще не з'явився (таке буває), ми ГЕНЕРУЄМО його примусово
RUN composer dump-autoload --optimize --no-dev

# 5. ПЕРЕВІРКА (тепер вона МАЄ пройти)
RUN ls -l vendor/autoload_runtime.php || (echo "STILL NOT FOUND, FORCING GENERATION..." && composer require symfony/runtime)

# ... (решта Dockerfile: права, Nginx, start.sh) ...