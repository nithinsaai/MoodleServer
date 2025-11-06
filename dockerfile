FROM php:8.3-apache

# Install Moodle dependencies
RUN apt-get update 

# RUN apt-get install -y \
#     libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev libzip-dev git unzip mariadb-client && \
#     docker-php-ext-install mysqli gd xml zip intl opcache && \
#     docker-php-ext-enable mysqli gd zip intl opcache && \
#     a2enmod rewrite && \
#     rm -rf /var/lib/apt/lists/*

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy config template (used on first run)
COPY config.template.php /var/www/html/config.php

WORKDIR /var/www/html
