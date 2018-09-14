#!/usr/bin/env bash

if [ ${APP_ROLE} == "queue-worker" ]; then
    # Go to the temp directory.
    cd /tmp
    # Create the supervisor configuration file for the Laravel queue worker.
    sudo touch /var/log/queue-worker.log
    curl https://raw.githubusercontent.com/RoyalBoroughKingston/ck-scripts/master/queue-worker/laravel-worker.conf | sudo tee /etc/supervisor/conf.d/laravel-worker.conf

    # Restart supervisor.
    sudo service supervisord stop
    sudo service supervisord start
fi
