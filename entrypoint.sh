#!/bin/bash
set -e

# Ensure moodledata permissions are correct
mkdir -p /var/www/moodledata
chown -R www-data:www-data /var/www/moodledata

# Enable rewrite just to be sure
a2enmod rewrite >/dev/null 2>&1

echo "🚀 Starting Apache..."
exec apache2-foreground
