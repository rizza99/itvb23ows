# Use an official PHP runtime as a parent image
FROM php:7.4-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
COPY ../php /var/www/html