FROM php:8.2-apache

# Install MySQL drivers for PHP
RUN docker-php-ext-install pdo_mysql mysqli

# Copy your app into the container
COPY . /var/www/html/
