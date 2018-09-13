#!/usr/bin/env bash

if [ ${APP_ROLE} == "queue-worker" ]; then
    # Go to the temp directory.
    cd /tmp

    # Install and start supervisor.
    sudo pip install supervisor
    curl -O https://raw.githubusercontent.com/Supervisor/initscripts/master/redhat-init-mingalevme
    sed -e "s/^PREFIX=\/usr$/PREFIX=\/usr\/local/" redhat-init-mingalevme > supervisord
    chmod 755 supervisord
    sudo chown root.root supervisord
    sudo mv supervisord /etc/init.d
    echo_supervisord_conf | sudo tee /etc/supervisord.conf
    cat /var/www/html/scripts/laravel-worker.conf | sudo tee -a /etc/supervisord.conf
    sudo /etc/init.d/supervisord start
    sudo chkconfig --add supervisord
    sudo chkconfig supervisord on
elif [ ${APP_ROLE} == "scheduler" ]; then
    # Copy the CRON entry.
    sudo cp /var/www/html/scripts/laravel-scheduler /etc/cron.d/laravel-scheduler
fi
