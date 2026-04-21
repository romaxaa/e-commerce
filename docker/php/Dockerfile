FROM php:8.2-fpm

# Устанавливаем расширения
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Опционально: другие расширения
RUN docker-php-ext-install bcmath

# Создаем директорию
WORKDIR /var/www

# Запускаем PHP-FPM
CMD ["php-fpm"]

RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-install intl
