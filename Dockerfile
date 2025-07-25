# Base PHP image with Apache
FROM php:8.3-apache

# Copy Zscaler certificate
COPY Zscaler.pem /usr/local/share/ca-certificates/Zscaler.crt

RUN a2enmod rewrite

# Set the correct document root in Apache config
COPY ./docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Copy full app (including public/)
COPY . /var/www

# Set working directory inside the container
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql

RUN update-ca-certificates

RUN pecl install redis && docker-php-ext-enable redis \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install bcmath

RUN docker-php-ext-install sockets

COPY xdebug.ini /usr/local/etc/php/conf.d/

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer