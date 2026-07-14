# Start with the official PHP image with Apache
FROM php:8.2-apache

# Install MySQL drivers for PDO
RUN docker-php-ext-install pdo_mysql

# Copy the application into the web server's document root
COPY app/ /var/www/html/
