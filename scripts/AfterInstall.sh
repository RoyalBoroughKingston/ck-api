#!/usr/bin/env bash

# Go to the install directory.
cd /var/www/html

# Download the .env file from AWS Secrets Manager.
echo "Importing .env file..."
aws secretsmanager get-secret-value --secret-id ck-api-env-production --region eu-west-1 | \
python -c "import json,sys;obj=json.load(sys.stdin);print obj['SecretString'];" > .env

# Install NPM dependencies.
npm install
npm run prod

# Install composer dependencies.
export COMPOSER_HOME="$HOME/.config/composer/"
composer install --no-interaction --no-dev

# Run migrations.
php artisan migrate --force

# Cache the config.
php artisan config:cache

# Decrypt the OAuth keys.
php artisan ck:decrypt-oauth-keys \
--public-key-name=oauth-public-production.key \
--private-key-name=oauth-private-production.key
