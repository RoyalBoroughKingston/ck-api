#!/usr/bin/env bash

if [ -d "/etc/supervisor" ]; then
    # Create the supervisor configuration file for the Laravel queue worker.
    curl https://raw.githubusercontent.com/RoyalBoroughKingston/ck-scripts/master/queue-worker/laravel-worker.conf | sudo tee /etc/supervisor/conf.d/laravel-worker.conf

    # Restart supervisor.
    sudo supervisorctl reread
    sudo supervisorctl reload
fi
