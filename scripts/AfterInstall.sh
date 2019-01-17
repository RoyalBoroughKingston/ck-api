#!/usr/bin/env bash

# Exit on first error.
set -e

# Set variables.
DOCUMENT_ROOT=/var/www/html
SECRET_ID=ck-api-env-production
SECRET_REGION=eu-west-1

# Go to the install directory and set permissions.
sudo chown -R ec2-user:nginx ${DOCUMENT_ROOT}
cd ${DOCUMENT_ROOT}

# Download the .env file from AWS Secrets Manager.
echo "Importing .env file..."
aws secretsmanager get-secret-value --secret-id ${SECRET_ID} --region ${SECRET_REGION} | \
    python -c "import json,sys;obj=json.load(sys.stdin);print obj['SecretString'];" > .env

# Install NPM dependencies.
echo $(whereis npm)
/usr/bin/npm install
/usr/bin/npm run prod

# Install composer dependencies.
export COMPOSER_HOME="$HOME/.config/composer/"
composer install --no-interaction --no-dev --optimize-autoloader

# Run migrations.
php artisan migrate --force

# Cache.
php artisan config:cache
php artisan route:cache

# Decrypt the OAuth keys.
php artisan ck:decrypt-oauth-keys \
    --public-key-name=oauth-public-production.key \
    --private-key-name=oauth-private-production.key
