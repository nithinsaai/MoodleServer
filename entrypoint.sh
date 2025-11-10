#!/bin/bash
set -e

MOODLE_DIR="/var/www/html"
MOODLE_DATA="/var/www/moodledata"
CONFIG_FILE="${MOODLE_DIR}/config.php"

echo "Starting Moodle container..."

# Wait for the DB to be ready
echo "Installing database client tool ..."
apt-get update && apt-get install -y mariadb-client

echo "Waiting for database (${MOODLE_DBHOST})..."

until mysqladmin ping -h"${MOODLE_DBHOST}" -u"${MOODLE_DBUSER}" -p"${MOODLE_DBPASS}" --ssl=0 --silent; do
    sleep 3
done
echo "Database is ready!"

# Install required packages for Moodle
echo "Installing Moodle dependencies..."

apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libmariadb-dev \
    cron
docker-php-ext-configure gd --with-freetype --with-jpeg
docker-php-ext-install -j$(nproc) gd intl mysqli opcache xml zip soap curl mbstring exif

# Create moodledata directory
mkdir -p "${MOODLE_DATA}"

# Install Moodle if not installed
if [ ! -f "${CONFIG_FILE}" ]; then
    echo "Installing Moodle for the first time..."

    php "${MOODLE_DIR}/admin/cli/install.php" \
    --chmod=2777 \
    --lang=en \
    --wwwroot="${MOODLE_URL}" \
    --dataroot="${MOODLE_DATA}" \
    --dbtype="${MOODLE_DBTYPE}" \
    --dbhost="${MOODLE_DBHOST}" \
    --dbname="${MOODLE_DBNAME}" \
    --dbuser="${MOODLE_DBUSER}" \
    --dbpass="${MOODLE_DBPASS}" \
    --fullname="Moodle LMS" \
    --shortname="Moodle" \
    --adminuser=admin \
    --adminpass=Admin@12345 \
    --adminemail=admin@example.com \
    --non-interactive \
    --agree-license

    echo "Moodle installation complete!"
else
    echo "Moodle already installed, skipping setup."
fi

# Ensure permissions
chown -R www-data:www-data "${MOODLE_DIR}" "${MOODLE_DATA}" "${CONFIG_FILE}"

echo "Starting Apache..."
exec apache2-foreground
