FROM php:8.0-apache

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# Habilitar los módulos necesarios
RUN a2enmod ssl \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http

# Definir ServerName para evitar warnings
RUN echo "ServerName eskua.com.uy" >> /etc/apache2/apache2.conf

# Copiar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Backend
WORKDIR /var/www/backend
COPY backend/composer.json ./

RUN composer install --no-dev --optimize-autoloader

COPY backend/ ./

# Frontend PHP
COPY front_php/ /var/www/html/

WORKDIR /var/www/html