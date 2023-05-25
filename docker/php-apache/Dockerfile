FROM php:7.4-apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
        libicu-dev \
        libzip-dev \
        zip \
        unzip \
        libreoffice \
        git

# Enable required PHP extensions
RUN docker-php-ext-install pdo_mysql intl zip

# activate url rewriting mode
RUN a2enmod rewrite
# deactivate ssl mode (https)
RUN a2dismod ssl

# fix: files permission
#RUN usermod -u 1000 www-data
#USER www-data
