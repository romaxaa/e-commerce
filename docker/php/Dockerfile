FROM php:8.2-fpm

# Устанавливаем redis
RUN pecl install redis && docker-php-ext-enable redis

WORKDIR /var/www