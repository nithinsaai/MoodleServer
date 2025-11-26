#!/bin/bash
set -e

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
    nano \
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

    echo "📦 Copying plugins into Moodle..."
    cp -r /plugin/* /var/www/html/local/
    php /var/www/html/admin/cli/upgrade.php --non-interactive

    echo "Moodle installation complete!"
else
    echo "Moodle already installed, skipping setup."
fi

# Run modular PHP scripts (in correct order)
run() {
    echo "➡ Running $1..."
    php /bootstrap/$1
}

run enable_scorm_debug.php
run enable_auth_methods.php
run enable_webservices.php
run enable_rest_protocol.php
run enable_developer_docs.php
run create_ws_admin.php

export WS_SERVICE_SHORTNAME="${WS_RGENIE_ADMIN_SERVICE_SHORTNAME}"
export WS_SERVICE_NAME="${WS_RGENIE_ADMIN_SERVICE_NAME}"
export WS_SERVICE_AUTHORIZED_ONLY=1
export WS_FUNCTIONS="${WS_RGENIE_ADMIN_SERVICE_FUNCTIONS}"
run create_external_service.php
run add_functions.php
run authorize_user.php

export WS_SERVICE_SHORTNAME="${WS_RGENIE_USER_SERVICE_SHORTNAME}"
export WS_SERVICE_NAME="${WS_RGENIE_USER_SERVICE_NAME}"
export WS_SERVICE_AUTHORIZED_ONLY=0
export WS_FUNCTIONS="${WS_RGENIE_USER_SERVICE_FUNCTIONS}"
run create_external_service.php
run add_functions.php

run enable_token_creation_for_authenticated_user.php
run configure_outgoing_mail.php
run create_role_users.php

echo "🎉 Moodle bootstrap complete!"

# Ensure permissions
chown -R www-data:www-data "${MOODLE_DIR}" "${MOODLE_DATA}"

echo "Starting Apache..."
exec apache2-foreground
