FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get -y update
RUN apt-get -y install git unzip nano rsync libicu-dev librabbitmq-dev

# Install php extentions
RUN pecl install xdebug redis amqp \
    && docker-php-ext-enable redis amqp xdebug \
    && docker-php-ext-install pdo pdo_mysql intl sockets

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

ENV TERM xterm-256color

# Install Composer
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

EXPOSE 9000
CMD php-fpm