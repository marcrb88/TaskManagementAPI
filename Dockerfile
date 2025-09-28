FROM php:8.2-apache

WORKDIR /var/www/symfony

COPY symfony/ ./

RUN apt-get update && apt-get install -y libzip-dev unzip git \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite
