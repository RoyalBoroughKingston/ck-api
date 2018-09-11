#!/usr/bin/env bash

# Go to the install directory.
cd /var/www/html

# Install NPM dependencies.
npm install
npm run prod

# Install composer dependencies.
composer install --no-interaction
