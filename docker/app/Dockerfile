# FROM php:8.1.3-fpm-alpine3.15
FROM php:8.1-fpm

RUN apt-get update && apt-get install -y wget git unzip \
    && pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug

WORKDIR /var/www/html
 
    
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000







