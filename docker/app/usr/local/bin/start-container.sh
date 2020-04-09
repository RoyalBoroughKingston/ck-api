#!/usr/bin/env bash

# Run migrations.
php /var/www/html/artisan migrate --force

# Cache config and routes.
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache

# Start Blackfire.
/etc/init.d/blackfire-agent start

# Start supervisor.
/usr/bin/supervisord
