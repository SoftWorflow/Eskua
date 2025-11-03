FROM php:8.0-apache

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite proxy proxy_http

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

COPY backend/composer.json ./

RUN composer install --no-dev --optimize-autoloader

COPY backend/ ./

COPY front_php/ /var/www/html/

WORKDIR /var/www/html