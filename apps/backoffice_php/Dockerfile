FROM php:8.0-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite proxy proxy_http

WORKDIR /var/www/html