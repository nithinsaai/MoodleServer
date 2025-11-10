# Use official Debian-based PHP + Apache image
FROM php:8.3-apache

# copy Moodle PHP settings
COPY php-moodle.ini /usr/local/etc/php/conf.d/php-moodle.ini

# Copy entrypoint script
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set working directory
WORKDIR /var/www/html

# Set entrypoint
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
